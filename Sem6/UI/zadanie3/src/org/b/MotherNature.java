package org.b;

import java.util.ArrayList;
import java.util.List;
import java.util.stream.Collectors;

public class MotherNature {
	
	public static List<Path> customize(int size){
		List<Path> result = new ArrayList<Path>(size);
		
		while(size-- > 0)
			result.add(GenAlgo.generate());
		return result;
	}
	
	public static List<Path> customize(List<Path> paths){
		int size = paths.size();
		List<Path> result = new ArrayList<Path>(size);
		
		//vyberie N najlepších
		result = paths.stream()
						.sorted((a, b) -> {
							if(a.getFitness() < b.getFitness())
								return 1;
							else if(a.getFitness() > b.getFitness())
								return -1;
							return 0;
						})
					  .limit((int)(MainGoldDigger.ELITISM * size))
					  .collect(Collectors.toList());
		
		if(MainGoldDigger.POPUL_GEN_LOG)
			System.out.println("vygenerovalo sa " + result.size() + " najlepších");
		
		//vygeneruje N náhodných
		for(int i=0 ; i<size * MainGoldDigger.RANDOM ; i++)
			result.add(GenAlgo.generate());
		
		if(MainGoldDigger.POPUL_GEN_LOG)
			System.out.println("vygenerovalo sa " + size * MainGoldDigger.RANDOM + " náhodných");
		
		int mut = 0;
		int cros = 0;
		for(Path p : paths){
			//MUTACIA
			if(Math.random() < MainGoldDigger.MUTATION){
				result.add(mutate(p));
				mut++;
			}
			if(result.size() == size)
				break;
			
			//KRIZENIE
			if(Math.random() < MainGoldDigger.CROSSING){
				result.add(cross(p, paths.get((int)(Math.random() * paths.size()))));
				cros++;
			}
			if(result.size() == size)
				break;
		}
		
		if(MainGoldDigger.POPUL_GEN_LOG){
			System.out.println("vygenerovalo sa " + mut + " mutovaných");
			System.out.println("vygenerovalo sa " + cros + " skrížených");
		}
		return result;
		
	}
	
	private static Path cross(Path p1, Path p2){
		int size = p1.getSource().length;
		int[] source = new int[p1.getSource().length];
		
		for(int i=0 ; i<size ; i++)
			source[i] = i%2 == 0 ? p1.getSource()[i] : p2.getSource()[i];
			
		return new Path(source);
	}

	private static Path mutate(Path p){
		int[] source = p.getSource();
		source[(int)(Math.random() * source.length)] = (int)(Math.random() * 255);
		return new Path(source);
	}
}
