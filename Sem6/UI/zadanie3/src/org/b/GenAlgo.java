package org.b;


import java.util.HashSet;
import java.util.List;
import java.util.Set;

import org.Utils;

public class GenAlgo{
	private int[] array;
	private int actIndex;
	private StringBuilder result;
	private double counter;
	private static Set<String> sources = new HashSet<String>();
	private static Set<String> pathsTotal = new HashSet<String>();
	private static int maxSourceGenerationTries = 0;
	private static int sumSourceGenerationTries = 0;
	private static int numberOfEmptyPaths = 0;
	private static int numberOfBuildPaths = 0;
	private static int numberOfGenSources = 0;
	private static int totalOperations = 0;
	private static double totalBuildTime = 0;
	
	public static Path generate(){
		numberOfGenSources++;
		int[] result = new int[MainGoldDigger.BITES];
		int num = 0;
		String res;
		do{
			res = "";
			for(int i=0 ; i<MainGoldDigger.BITES ; i++){
				result[i] = (int)(Math.random() * MainGoldDigger.VALUES);
				res += result[i];
			}
			num++;
		}while(sources.contains(res));
		
		sources.add(res);
		sumSourceGenerationTries += num;
		maxSourceGenerationTries = Math.max(maxSourceGenerationTries, num);
		
		if(MainGoldDigger.SOURCE_GEN_LOG)
			System.out.println("na " + num + " krát sa vygeneroval zdrojový kod: " + res);
		
		
		return new Path(result);
	}
	
	public void build(Path path, int size, int maxOperations){
		numberOfBuildPaths++;
		result = new StringBuilder();
		counter = actIndex = 0;
		this.array = path.getSource();
		
		long start = System.currentTimeMillis();
		while(result.length() < size && actIndex >= 0 && actIndex < MainGoldDigger.BITES && counter < maxOperations)
			process();

		start = System.currentTimeMillis() - start;
		totalBuildTime += start;
		if(MainGoldDigger.BUILD_LOG)
			System.out.println("aplikacia vykonala " + counter + " operacii za " + start + "ms a vyplula: " + result);

		totalOperations += counter;
		pathsTotal.add(result.toString());
		
		if(result.toString().isEmpty())
			numberOfEmptyPaths++;
		
		path.setPath(result.toString());
	}
	
	private void process(){
		counter++;
		String operator = getOperation(array[actIndex]);
		switch(operator){
			case "00" :
				inc(getAddressInt(array[actIndex++]));
				break;
			case "01" :
				dec(getAddressInt(array[actIndex++]));
				break;
			case "10" :
				jump(getAddressInt(array[actIndex++]));
				break;
			case "11" :
				show(array[actIndex++]);
				break;
		}
	}
	
	private void inc(int num){
		array[num]++;
	}
	
	private void dec(int num){
		array[num]--;
	}
	
	private void jump(int num){
		actIndex = num; 
	}
	
	private void show(int num){ 
		result.append(getLetter(num)); 
	}
	
	public void print(){
		for(int i=0 ; i<MainGoldDigger.BITES ; i++)
			System.out.print(array[i] + " ");
		System.out.println("");
	}
	
	public static void printStat(){
		System.out.println("celkovo bolo vygenerovaných " + numberOfGenSources + " zdrojákov na " + sumSourceGenerationTries + " pokusov a navačší pokus bol " + maxSourceGenerationTries + " opakovaní");
		System.out.println("celkovo bolo sbuildovaných " + numberOfBuildPaths + " ciest a s toho ich bolo " + numberOfEmptyPaths + " prázdnych");
		System.out.println("celková dĺžka buidlovania bola " + totalBuildTime + "ms a vykonalo sa " + totalOperations + " operácii");
	}
	
	private String getOperation(int num){
		return Utils.getBytesOfInt(num).substring(0, 2);
	}
	
	private int getAddressInt(int num){
		return Utils.getIntValue(Utils.getBytesOfInt(num).substring(2, 8).getBytes());
	}
	
	private char getLetter(int tmp){
		byte[] num = Utils.getBytesOfInt(tmp).getBytes();
		int count = 0;
		for(int i=0 ; i<num.length ; i++)
			if(num[i] == 49)
				count++;
		
		if(count <= 2)
			return 'h';
		if(count <= 4)
			return 'd';
		if(count <= 6)
			return 'p';
		
		return 'l';
	}

	public static void analyze(List<Path> paths, int num) {
		int maxTreasures = 0;
		int minStep = -1;
		float maxFitness = 0;
		float sumFitness = 0;
		float sumTreasures = 0;
		float sumStreps = 0;
		Path best = null;
		for(Path p : paths){
			if(p.getTreasures() > maxTreasures)
				maxTreasures = p.getTreasures();
			
			if(minStep < 0 || minStep > p.getSteps())
				minStep = p.getSteps();
			
			if(maxFitness < p.getFitness()){
				maxFitness = p.getFitness();
				best = p;
			}
			
			sumFitness	 = p.getFitness();
			sumTreasures = p.getTreasures();
			sumStreps 	 = p.getSteps();
		}
		float size = paths.size();
		System.out.print("num: " + num +", operacii: "+ totalOperations +", " + best + ", paths/sources: " + pathsTotal.size() + "/" + sources.size() + ", count: " + size);
		System.out.println(", avgFit: " + sumFitness / size + ", avgTrea: " + sumTreasures / size + ", avgSteps: " + sumStreps / size);
	}
}
