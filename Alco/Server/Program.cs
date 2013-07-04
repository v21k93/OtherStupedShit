using System;
using System.Collections.Generic;
using Alchemy;
using Alchemy.Classes;
using Newtonsoft.Json;

namespace WebServer
{
    class Program
    {
        private static List<Client> Clients = new List<Client>();
        private static Client Client_console = null;
        private static WebSocketServer SocketServer;
        private static UInt16 ServerPort = 9095;
        private static UInt16 HeartBeat = 25;

        private static string GenerateSHA1(string str)
        {
            System.Security.Cryptography.SHA1 hash = System.Security.Cryptography.SHA1CryptoServiceProvider.Create();
            byte[] hashBytes = hash.ComputeHash(System.Text.Encoding.UTF8.GetBytes(str));

            str = "";

            foreach (byte b in hashBytes)
            {
                str += string.Format("{0:x2}", b);
            }

            return str.ToUpper();
        }

        static void Main(string[] args)
        {
            SocketServer = new WebSocketServer(ServerPort, System.Net.IPAddress.Any)
            {
                OnReceive = OnReceive,
                OnConnected = OnConnect,
                OnDisconnect = OnDisconnect,
                TimeOut = new TimeSpan(0, 5, 0)
            };

            SocketServer.Start();
            System.Threading.Timer t = new System.Threading.Timer(timerTick, null, 0, HeartBeat * 1000);

            Console.WriteLine("WebServer Successfuly Started !");
            Console.Write(">");

            string _text = string.Empty;

            while (_text.ToLower() != "!exit")
            {
                _text = Console.ReadLine().Trim();
                ConsoleSend(_text);
                Console.Write(">");
            }

            SocketServer.Stop();

            SocketServer = null;

            Console.WriteLine("Press any key to continue...");
            Console.ReadLine();
            Environment.Exit(0);
        }

        private static void timerTick(object obj)
        {
            if (Clients.Count < 1)
                return;

            double unixTime = (DateTime.UtcNow - new DateTime(1970, 1, 1, 0, 0, 0)).TotalMilliseconds;
            Data data = new Data { comm = Command.PING, data = unixTime.ToString() };
            send_clients(JsonConvert.SerializeObject(data));
        }

        private static void ConsoleWrite(string _text)
        {
            if (_text == null || _text == "")
                return;

            Console.WriteLine(_text);
            if (Client_console != null)
            {
                Data data = new Data { comm = Command.CONSOLE_MESSAGE, data = _text };
                send_client(JsonConvert.SerializeObject(data), Client_console);
            }
        }

        private static void ConsoleSend(string _text)
        {
            if (_text == null || _text == "" || Clients.Count < 1)
                return;

            Data data;

            switch (_text.ToLower())
            {
                case "!ping":
                    double unixTime = (DateTime.UtcNow - new DateTime(1970, 1, 1, 0, 0, 0)).TotalMilliseconds;
                    data = new Data { comm = Command.PING, data = unixTime.ToString() };
                    send_clients(JsonConvert.SerializeObject(data));
                    break;
                case "!kill":
                    break;
                case "!show":
                    showUsers();
                    break;
                default:
                    data = new Data { comm = Command.MESSAGE, data = new { name = "Server", message = _text } };
                    send_clients(JsonConvert.SerializeObject(data));
                    break;
            }
        }

        private static void OnDisconnect(UserContext context)
        {
#if(DEBUG)
            ConsoleWrite("Client Disconnected : " + context.ClientAddress);
#endif
            Client user = null;
            foreach (Client kvp in Clients)
                if (kvp.context.ClientAddress == context.ClientAddress)
                    user = kvp;

            if (user == null)
                return;

            if (Client_console != null && user == Client_console)
                Client_console = null;

            Clients.Remove(user);
        }

        private static void OnConnect(UserContext context)
        {
#if(DEBUG)
            ConsoleWrite("Client Connection From : " + context.ClientAddress);
#endif

            Client me = new Client { context = context };
            Clients.Add(me);
        }

        private static void OnReceive(UserContext context)
        {
            string json = string.Empty;
            dynamic obj = null;

            try
            {
                json = context.DataFrame.ToString();
                if(json == string.Empty || ! json.Contains("}"))
                    return;

                obj = JsonConvert.DeserializeObject(json);
                if (obj == null)
                    return;
            }
            catch (Exception e)
            {
#if(DEBUG)
                ConsoleWrite(e.ToString());
#endif
                return;
            }
#if(DEBUG)
            ConsoleWrite("Received Data : " + json);
#endif
            Client client = getClient(context.ClientAddress);

            if (client == null)
                return;

            if (client.user == null && (int)obj.comm != (int)Command.CHANGE_NAME)
            {
                client.context.Dispose();
                return;
            }

            double unixTime = (DateTime.UtcNow - new DateTime(1970, 1, 1, 0, 0, 0)).TotalMilliseconds;

            switch ((int)obj.comm)
            {
                case (int)Command.MESSAGE:
                    if((string)obj.data.message == "console" && client.user.id == 1 && Client_console == null)
                    {
                        Client_console = client;
                        break;
                    }
                    if (Client_console != null && client == Client_console)
                    {
                        ConsoleSend((string)obj.data.message);
                        break;
                    }
                    if (unixTime - client.last_rec < 250)
                    {
                        if (++client.user.warnings > 3)
                        {
                            Data data;
                            if (++client.user.errors > 3)
                            {
                                context.Dispose();
                                return;
                            }
                            else
                            {
                                data = new Data { comm = Command.MUTE, data = ((client.user.errors) * 10).ToString() };
                                send_client(JsonConvert.SerializeObject(data), client);
                                client.user.warnings = 0;
                            }
                        }
                    }
                    else
                        client.user.warnings = 0;

                    client.last_rec = unixTime;
                    send_clients(JsonConvert.SerializeObject(obj));
                    break;
                case (int)Command.MESSAGE_STATUS:
                    send_clients(JsonConvert.SerializeObject(obj), UInt16.Parse((string)obj.data.id));
                    break;
                case (int)Command.PING:
                    unixTime = (DateTime.UtcNow - new DateTime(1970, 1, 1, 0, 0, 0)).TotalMilliseconds;
                    double unixLastTime = double.Parse((string)obj.data);
                    client.ping = Convert.ToUInt16(unixTime - unixLastTime);
                    break;
                case (int)Command.CHANGE_NAME:
                    client.user = new User((string)obj.data.name, (ushort)obj.data.id);
                    break;
            }
        }

        private static Client getClient(System.Net.EndPoint ip)
        {
            return Clients.Find(delegate(Client tmp) { return tmp.context.ClientAddress == ip; });
        }

        private static void send_clients(string message, UInt16 user_id = 0)
        {
            try
            {
                foreach (Client u in Clients)
                    if (user_id == 0 || u.user.id != user_id)
                        u.context.Send(message);
            }
            catch (Exception e)
            {
                ConsoleWrite(e.ToString());
            }
        }

        private static void send_client(string message, Client user)
        {
            try
            {
                user.context.Send(message);
            }
            catch (Exception e)
            {
                ConsoleWrite(e.ToString());
            }
        }

        private static void showUsers()
        {
            Console.Clear();
            string _str = String.Format("\n|{0,4} | {1,25} | {2,19} | {3,4}|", "ID", "Name", "IP", "Ping") + Environment.NewLine +
            "_______________________________________________________________" + Environment.NewLine;
            foreach (Client kvp in Clients)
                _str += String.Format("|{0,4} | {1,25} | {2,19} | {3,4}|", kvp.user != null ? kvp.user.id : 0, kvp.user != null ? kvp.user.name : "", kvp.context.ClientAddress, kvp.ping) + Environment.NewLine;

            _str += "_______________________________________________________________" + Environment.NewLine;

            ConsoleWrite(_str);
        }
    }
}
