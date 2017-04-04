package org;

import java.awt.Color;
import java.awt.Graphics2D;
import java.util.HashMap;
import java.util.Map;
import java.util.Map.Entry;

public class MainZadanie2 extends GCanvasLoop{
	private Mapa map = new Mapa();
	private Map<String, Auto> auta = new HashMap<String, Auto>();
	public MainZadanie2() {
		super(new GVector2f(800, 600), "Zadanie2", false, 60);
		init();
		

//((cervene 2 3 2 h)(oranzove 2 1 1 h)(zlte 3 2 1 v)(fialove 2 5 1 v)
//(zelene 3 2 4 v)(svetlomodre 3 6 3 h)(sive 2 5 5 h)(tmavomodre 3 1 6 v))
	}

	public static void main(String[] args) {
		MainZadanie2 app = new MainZadanie2();
		app.start();
	}
	public void init(){
		auta.clear();
		auta.put("yellow", new Auto(Color.YELLOW, new GVector2f(0, 1), 3, Auto.VER, map));
		auta.put("orange", new Auto(Color.ORANGE, new GVector2f(0, 0), 2, Auto.HOR, map));
		auta.put("red", new Auto(Color.RED, new GVector2f(1, 2), 2, Auto.HOR, map));
		auta.put("magenta", new Auto(Color.MAGENTA, new GVector2f(0, 5), 2, Auto.VER, map));
		auta.put("green", new Auto(Color.GREEN, new GVector2f(3, 1), 3, Auto.VER, map));
		auta.put("cyan", new Auto(Color.CYAN, new GVector2f(2, 5), 3, Auto.HOR, map));
		auta.put("gray", new Auto(Color.GRAY, new GVector2f(4, 4), 2, Auto.HOR, map));
		auta.put("blue", new Auto(Color.BLUE, new GVector2f(5, 0), 3, Auto.VER, map));
	}
	@Override
	protected void input() {
	}

	@Override
	protected void update(float delta) {
	}

	@Override
	public void render(Graphics2D g2) {
		for(Entry<String, Auto> e : auta.entrySet())
			e.getValue().draw(g2);
	}

	@Override
	public void cleanUp() {
	}

}
