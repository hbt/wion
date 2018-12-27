extern crate byteorder;
extern crate rand;

use std::net::UdpSocket;
use std::net::{IpAddr, Ipv4Addr, SocketAddr, SocketAddrV4};
use rand::Rng;
use std::str;

mod wion_comm;

use std::thread;
use std::time;
use std::env;

fn main() {
    let ip = std::env::args().into_iter().skip(1).next().unwrap();
    let cmd = std::env::args().into_iter().skip(3).next().unwrap();

    let msg_sock = wion_comm::msg_listener_setup();
    let switch_ip: &str = &*ip;

    if cmd == "status" {
        wion_comm::get_switch_status(switch_ip, &msg_sock);
    }

    if cmd == "discover" {
        let send_broadcast_sock = wion_comm::broadcast_setup();
        wion_comm::send_broadcast(&send_broadcast_sock);
    }

    if cmd == "on" {
        wion_comm::send_switch_toggle(true, switch_ip, &msg_sock);
    }

    if cmd == "off" {
        wion_comm::send_switch_toggle(false, switch_ip, &msg_sock);
    }
}

