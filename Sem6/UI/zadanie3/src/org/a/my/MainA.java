package org.a.my;

public class MainA{

	public static void main(String[] args) {
		Map m = new Map(9, 9);
		m.setNRandomRocks(10);
		
		for(int i=0 ; i<5 ; i++)
			m.solve();
		m.showStatus();
	}

}
