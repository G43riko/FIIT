package org.chat.utils;

import java.util.ArrayList;

public class Log {
	public final static boolean CONSTRUCTORS 	= false;
	public final static boolean CONNECTION 		= false;
	public final static boolean EXCEPTIONS 		= true;
//	public final static boolean FILE_MESSAGE 	= false;
	public final static boolean PING_MESSAGE 	= false;
	public final static boolean DEBUG 			= true;
	
	private final static ArrayList<LogMessage> logs = new ArrayList<LogMessage>();
	public static final boolean FIXER = true;
	
	public static void write(String message, boolean val){
		write(message, null, val);
	}
	
	public static void write(String message,Exception error, boolean val){
		
		if(error != null)
		
		logs.add(new LogMessage(message, error));
		if(val){
			System.out.println(message);
			if(error != null)
				error.printStackTrace();
		}
	}
	
	public static void printLogs(){
		logs.stream()	
			.sorted((a, b) -> Long.compare(a.getCreated(), b.getCreated()))
			.map(a -> a.getCreated() + ": " + a.getText() + " == " + a.getError())
			.forEach(System.out::println);
	}
}
