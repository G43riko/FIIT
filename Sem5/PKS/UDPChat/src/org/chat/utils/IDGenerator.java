package org.chat.utils;

import java.util.HashSet;
import java.util.Set;

public class IDGenerator {
	private static Set<Integer> ides = new HashSet<Integer>();
	private static int id;
	
	public static int getId(){
		do{
			id = (int)(Math.random() * 2147483647);
		}while(ides.contains(id));
		
		ides.add(id);
		
		return id;
	}
}
