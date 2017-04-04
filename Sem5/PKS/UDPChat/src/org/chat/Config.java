package org.chat;

import org.chat.utils.Utils;

public class Config {
	public final static String GUI_TITLE			= "UDP Chat";
	public final static String GUI_DEFAULT_ADRESS 	= Utils.getMyIP();
	public final static String GUI_DEFAULT_PORT		= "9001";
	
	public final static int CHAT_TOTAL_MAX_MSG_SIZE = 65000; //65535
	public final static int CHAT_DEFAULT_MSG_SIZE 	= 60000;
	public final static int CHAT_CRC_LENGTH			= 8;
	
	public final static int PING_LOOP_FREQUENCY		= 5000;
	public final static int PING_WAITING_TIME		= 3000;
	public final static int PING_CHECKING_TIME		= 5000;
	public static final int CHAT_MAX_SEND_NUMBER 	= 5;
	public static final String CHAT_BAD_MSG 		= "/*bad*/";
}
