package org.chat.core;

import java.io.IOException;
import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.net.SocketException;

import org.chat.Config;
import org.chat.UDPChat;
import org.chat.utils.Log;
import org.chat.utils.Utils;

public final class Server extends Connection{
	public static final byte CLIENT_SEND_MSG = 0;
	public static final byte CLIENT_DISCONNECT = 1;
	public static final byte CLIENT_CONNECT = 2;
	public static final byte CLIENT_PING = 3;
	public static final byte SERVER_PING = 4;
	public static final byte CLIENT_RECIEVE_MSG_OK = 5;
	public static final byte CLIENT_RECIEVE_MSG_ERROR = 6;

	private Thread 			check;
	private long 			requestTime = 0;
	
	
	public Server(UDPChat parent) {
		super(parent);
		Log.write("zaèal konštruktor objektu Server", Log.CONSTRUCTORS);
		
		try {
			socket = new DatagramSocket(parent.getPort());
			listen();
		} catch (SocketException e) {
			Log.write("Nepodarilo sa vytvori� server socket", e, Log.EXCEPTIONS);
		}
		Log.write("skonèil konštruktor objektu Server", Log.CONSTRUCTORS);
	}

	public void startMessageChecking() {
		lastContact = System.currentTimeMillis();
		check = new Thread(new Runnable(){
			public void run() {
				while(isRunning()){
					if(System.currentTimeMillis() - lastContact > Config.PING_CHECKING_TIME){
						if(requestTime == 0){
							requestTime = System.currentTimeMillis();
							getParent().getMessageManager().createPingMessage();
						}
						else if(System.currentTimeMillis() - requestTime > Config.PING_WAITING_TIME){
							getParent().oponenetDisconect();
							requestTime = 0;
						}
					}
					else
						if(requestTime > 0)
							requestTime = 0;
					
					Utils.sleep(Config.PING_LOOP_FREQUENCY);
				}
			}
		});
		check.start();
	}

	
	public void write(String message){
		if(!isRunning())
			return;
		try {
			DatagramPacket outpacket = getPacket(message, 
												 InetAddress.getByName(getParent().getIp()), 
												 getParent().getPort() + 1);
			
			socket.send(outpacket);
			Log.write("server odoslal správu: " + message, Log.CONNECTION);
		} catch (IOException e) {
			Log.write("zo servera sa epodarilo odosla� správu: " + message, e, Log.EXCEPTIONS);
		}
	}
	
	private void listen(){
		listen = new Thread(new Runnable(){
			@Override
			public void run() {
				while(isRunning()){
					try {
						byte[] block = new byte[Config.CHAT_TOTAL_MAX_MSG_SIZE];
						DatagramPacket inpacket = new DatagramPacket(block, block.length);
						socket.receive(inpacket);
						

						if(getParent().getIp() != inpacket.getAddress().getHostName())
							getParent().setIp(inpacket.getAddress().getHostAddress());
						
						proccessMessage(new String(inpacket.getData(), 0, inpacket.getLength()));
					} catch (IOException e) {
						Log.write("Server socket bol zatvorený", e ,Log.EXCEPTIONS);
					}
				}
				socket.close();
			}
		});
		
		listen.start();
	}

	@Override
	public boolean isServer() {return true;}
}
