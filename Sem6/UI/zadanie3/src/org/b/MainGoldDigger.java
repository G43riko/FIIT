package org.b;

import java.util.List;

import org.GVector2f;

public class MainGoldDigger{
	/**
	 * určuje počet pamäťových blokov
	 */
	public final static int BITES = 64;
	
	/**
	 * určuje najvačšie číslo ktoré sa môže nachádzať v kóde
	 */
	public final static int VALUES = 255;
	
	/**
	 * určuje váhu hodnoty prejdených krokov
	 */
	public final static int STEPS_RATION = 30;
	
	/**
	 * určuje váhu hodnoty nájdených pokladov
	 */
	public final static int TREASURES_RATION = 70;
	
	/**
	 * určuje kolko operácii sa má vykonať kým sa program ukončí
	 */
	public final static int MAX_OPERATIONS = 500;
	
	/**
	 * určuje kolko krát sa má vytvoriť nová generácia
	 */
	public final static int LOOPS = 50;
	
	/**
	 * určuje kolko je jedincov v populácii
	 */
	public final static int POPULATON = 10000;
	
	/**
	 * určuje faktor mutovanie pri tvorbe novej generácie
	 */
	public final static float MUTATION = 0.05f;
	
	/**
	 * určuje faktor kríženia pri tvorbe novej generácie
	 */
	public final static float CROSSING = 0.95f;
	
	/**
	 * určuje faktor elitarizmu pri tvorbe novej generácie
	 */
	public final static float ELITISM = 0.05f;
	
	/**
	 * určuje faktor novej krvi pri tvorbe novej generácie
	 */
	public final static float RANDOM = 0.20f;
	
	/**
	 * určuje či sa má hladať lepšie cesta aj po nájdený všetkých pokladov
	 */
	public final static boolean FIND_BEST_PATH 	= true;

	public final static boolean SOURCE_GEN_LOG 	= false;
	public final static boolean POPUL_GEN_LOG 	= false;
	public final static boolean BUILD_LOG 		= false;
	
	
	public static void main(String[] args) {
		GenAlgo alg = new GenAlgo();
		GVector2f mapSize = new GVector2f(7, 7);
		GVector2f startPosition = new GVector2f(3, 0);
		
		Map map = new Map(mapSize, new GVector2f(1, 2), new GVector2f(4, 1), 
						  new GVector2f(2, 4), new GVector2f(4, 5), new GVector2f(6, 3));
		
	
		List<Path> paths = MotherNature.customize(POPULATON);
		
		for(int i=0 ; i<LOOPS ; i++){
			for(Path p : paths){
				if(!p.isBuild())
					alg.build(p, map.getTiles(), MAX_OPERATIONS);
				if(!p.isSolved())
					map.solve(p, new GVector2f(startPosition));
			}
			GenAlgo.analyze(paths, i);
			
			paths = MotherNature.customize(paths);
		}
		GenAlgo.printStat();
	}
	
	
}