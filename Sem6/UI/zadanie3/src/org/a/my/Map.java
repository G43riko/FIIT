package org.a.my;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashSet;
import java.util.List;
import java.util.Set;
import java.util.stream.Collectors;

import org.GVector2f;

public class Map{
	public final static char HORIZONTAL = 'c';
	public final static char VERTICAL = 'v';
	public final static char UNVISITED = '0';
	public final static char ROCK = 'X';
	public final static char T = 'T';
	public final static char R = 'R';
	public final static char B = 'B';
	public final static char L = 'L';
	private GVector2f size;
	private char counter = 'a';
	private Tile[][] map;
	private List<Tile> startList;
	
	public Map(int x, int y){
		size = new GVector2f(x, y);
		map = initMap(size);
		startList = getStartTiles();
	}
	
	public void setNRandomRocks(int num){
		for(int i=0 ; i<num ; i++){
			GVector2f v = size.getRandomInt();
			map[v.getXi()][v.getYi()].setVisited(ROCK);
		}
	}
	
	public void solve(){
		Tile t = getRandomFromList(startList);
		if(solve(t, null))
			counter++;
		startList.remove(t);
	}
	
	private boolean solve(Tile s, Tile parent){
		if(s == null){
			System.out.println("Nemá odkial zaèa");
			showStatus();
			System.exit(1);
		}
		s.setParent(parent);
		s.setVisited(counter);
		Tile next = getNext(s);
//		System.out.println(s + " => " + next + ", counter: " + counter);
		if(next == s){
			return true;
		}
		
		if(next != null)
			return solve(next.setFrom(s.getFrom()), s);
		
		Tile t = getRandomFromList(getNeighboards(s, true));
		
		if(t != null)
			return solve(map[t.getPosition().getXi()][t.getPosition().getYi()].setFrom(calcDir(s, t)), s);
		
		while((s = s.getParent()) != null)
			s.setVisited('0');
		return false;
		
	}
	
	private Tile getNext(Tile t){
		if(t.getFrom() == R){
			if(t.getPosition().getX() == 0)
				return t;
			if(get(-1, 0, t).getVisited() == UNVISITED)
				return get(-1, 0, t);
		}

		else if(t.getFrom() == L){
			if(t.getPosition().getX() + 1 == size.getX())
				return t;
				
			if(get(+1, 0, t).getVisited() == UNVISITED)
				return get(+1, 0, t);
		}

		else if(t.getFrom() == B){
			if(t.getPosition().getY() == 0)
				return t;
			if(get(0, -1, t).getVisited() == UNVISITED)
				return get(0, -1, t);
		}

		else if(t.getFrom() == T){
			if(t.getPosition().getY() + 1 == size.getY())
				return t;
			if(get(0, +1, t).getVisited() == UNVISITED)
				return get(0, +1, t);
		}
		return null;
	}
	
	private Tile get(int x, int y, Tile t){
		return map[t.getPosition().getXi() + x][t.getPosition().getYi() + y];
	}
	
	private char calcDir(Tile from, Tile to){
		if(from.getPosition().getX() + 1 == to.getPosition().getX())
			return L;
		if(from.getPosition().getX() - 1 == to.getPosition().getX())
			return R;
		if(from.getPosition().getY() + 1 == to.getPosition().getY())
			return T;
		if(from.getPosition().getY() - 1 == to.getPosition().getY())
			return B;
		
		System.out.println("NO NEIGHBOARS");
		System.exit(1);
		return 0;
	}
	
	private static <T> T getRandomFromList(List<T> tiles){
		if(tiles.isEmpty())
			return null;
		return tiles.get((int)(tiles.size() * Math.random()));
	}
	
	private static Tile[][] initMap(GVector2f size){
		Tile[][] map = new Tile[size.getXi()][size.getYi()];
		size.forEach(a -> {
			map[a.getXi()][a.getYi()] = new Tile(a, calcDistFromBorder(a, size));
		});
		return map;
	}
	
	public void showDistancesFromBorder(){
		for(int j=0 ; j<size.getY() ; j++){
			for(int i=0 ; i<size.getX() ; i++)
				System.out.print(new DecimalFormat("000").format(map[i][j].calcBorderDist()) + " ");
			System.out.println();
		}
	}
	
	private static int calcDistFromBorder(GVector2f position, GVector2f size){
		return Math.min(Math.min(position.getXi() + 1, size.getXi() - position.getXi()), 
				 		Math.min(position.getYi() + 1, size.getYi() - position.getYi()));
	}
	
	private List<Tile> getNeighboards(Tile t, boolean onlyFree){
		List<Tile> list = new ArrayList<Tile>(8);
		
		if(t.getPosition().getX() > 0)
			list.add(get(-1, 0, t));
		if(t.getPosition().getY() > 0)
			list.add(get(0, -1, t));
		if(t.getPosition().getX() + 1 < size.getX())
			list.add(get(+1, 0, t));
		if(t.getPosition().getY() + 1 < size.getY())
			list.add(get(0, +1, t));
		
		if(onlyFree)
			return list.stream().filter(a -> a.getVisited() == UNVISITED).collect(Collectors.toList());
		
		return list;
	}
	private List<Tile> getStartTiles(){
		List<Tile> list = new ArrayList<Tile>((int)size.sum() * 2);
		
		for(int i=0 ; i<size.getX() ; i++){
			if(map[i][0].getVisited() == UNVISITED)
				list.add(map[i][0].setFrom(T));
			if(map[i][size.getYi() - 1].getVisited() == UNVISITED)
				list.add(map[i][size.getYi() - 1].setFrom(B));
		}
		for(int i=0 ; i<size.getY() ; i++){
			if(map[0][i].getVisited() == UNVISITED)
				list.add(map[0][i].setFrom(L));
			if(map[size.getXi() - 1][i].getVisited() == UNVISITED)
				list.add(map[size.getXi() - 1][i].setFrom(R));
		}
		return list;
//		return new ArrayList<Tile>(new HashSet<Tile>(list));
	}
	
	//SHOWERS
	
	public void showStatus(){
		for(int j=0 ; j<size.getY() ; j++){
			for(int i=0 ; i<size.getX() ; i++)
				System.out.print(map[i][j].getVisited() + " ");
			System.out.println();
		}
	}
	
	public void showPositions(){
		for(int j=0 ; j<size.getY() ; j++){
			for(int i=0 ; i<size.getX() ; i++)
				System.out.print(map[i][j] + " ");
			System.out.println();
		}
	}
}
