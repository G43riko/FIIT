package org;


public final class Utils {
	
	public static void sleep(int ms){
		try {
			Thread.sleep(ms);
		} catch (InterruptedException e) {}
	}
	
}
