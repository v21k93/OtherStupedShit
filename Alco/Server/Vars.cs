using System;
using Alchemy.Classes;

namespace WebServer
{
    public class Client
    {
        public UserContext context { get; set; }
        public User user { get; set; }
        public UInt16 ping { get; set; }
        public double last_rec = (DateTime.UtcNow - new DateTime(1970, 1, 1, 0, 0, 0)).TotalMilliseconds;
    }

    public class User
    {
        public string name;
        public UInt16 id;
        public UInt16 warnings { get; set; }
        public UInt16 errors { get; set; }
        public User(string _name, UInt16 _id)
        {
            this.id = _id;
            this.name = _name;
        }
    }

    public class Data
    {
        public Command comm { get; set; }
        public dynamic data { get; set; }
    }

    public enum Command
    {
        MESSAGE_STATUS,
        MESSAGE,
        CHANGE_NAME,
        PING,
        KILL,
        MUTE,
        CONSOLE_MESSAGE,
        NULL = 255,
    };
}
