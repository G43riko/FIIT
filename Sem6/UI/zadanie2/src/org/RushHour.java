package org;

import java.util.*;

public class RushHour {
    static Map<String,String> pred = new HashMap<String,String>();
    static Queue<String> queue = new LinkedList<String>();
    
    static final int N = 6;
    static final int M = 6;
    static final int GOAL_R = 2;
    static final int GOAL_C = 5;

    static final String INITIAL = "333BCC" + "B22BCC" + "B.XXCC" + "22B..." + ".BB.22" + ".B2222";

    static final String HORZS = "23X";  // horizontal-sliding cars
    static final String VERTS = "BC";   // vertical-sliding cars
    static final String LONGS = "3C";   // length 3 cars
    static final String SHORTS = "2BX"; // length 2 cars
    static final char GOAL_CAR = 'X';
    static final char EMPTY = '.';      // empty space, movable into
    static final char VOID = '@';       // represents everything out of bound

    static String prettify(String state) {
        String EVERY_NTH = "(?<=\\G.{N})".replace("N", String.valueOf(N));
        return state.replaceAll(EVERY_NTH, "\n");
    }
    
    static int rc2i(int r, int c) {
        return r * N + c;
    }

    static boolean isType(char entity, String type) {
        return type.indexOf(entity) != -1;
    }

    static int length(char car) {
        return
            isType(car, LONGS) ? 3 :
            isType(car, SHORTS) ? 2 :
            0/0; // a nasty shortcut for throwing IllegalArgumentException
    }

    static char at(String state, int r, int c) {
        return (inBound(r, M) && inBound(c, N)) ? state.charAt(rc2i(r, c)) : VOID;
    }
    static boolean inBound(int v, int max) {
        return (v >= 0) && (v < max);
    }

    static boolean isGoal(String state) {
        return at(state, GOAL_R, GOAL_C) == GOAL_CAR;
    }

    static int countSpaces(String state, int r, int c, int dr, int dc) {
        int k = 0;
        while (at(state, r + k * dr, c + k * dc) == EMPTY)
            k++;
        return k;
    }

    static void propose(String next, String prev) {
        if (!pred.containsKey(next)) {
            pred.put(next, prev);
            queue.add(next);
        }
    }
    
    static int trace(String current) {
        String prev = pred.get(current);
        int step = (prev == null) ? 0 : trace(prev) + 1;
        System.out.println(step);
        System.out.println(prettify(current));
        return step;
    }
    
    static void slide(String current, int r, int c, String type, int distance, int dr, int dc, int n) {
        r += distance * dr;
        c += distance * dc;
        char car = at(current, r, c);
        if (!isType(car, type)) return;
        final int L = length(car);
        StringBuilder sb = new StringBuilder(current);
        for (int i = 0; i < n; i++) {
            r -= dr;
            c -= dc;
            sb.setCharAt(rc2i(r, c), car);
            sb.setCharAt(rc2i(r + L * dr, c + L * dc), EMPTY);
            propose(sb.toString(), current);
            current = sb.toString(); // comment to combo as one step
        }
    }
    static void explore(String current) {
        for (int r = 0; r < M; r++)
            for (int c = 0; c < N; c++) {
                if (at(current, r, c) != EMPTY) continue;
                int nU = countSpaces(current, r, c, -1, 0);
                int nD = countSpaces(current, r, c, +1, 0);
                int nL = countSpaces(current, r, c, 0, -1);
                int nR = countSpaces(current, r, c, 0, +1);
                slide(current, r, c, VERTS, nU, -1, 0, nU + nD - 1);
                slide(current, r, c, VERTS, nD, +1, 0, nU + nD - 1);
                slide(current, r, c, HORZS, nL, 0, -1, nL + nR - 1);
                slide(current, r, c, HORZS, nR, 0, +1, nL + nR - 1);
            }
    }
    public static void main(String[] args) {
        propose(INITIAL, null);
        boolean solved = false;
        while (!queue.isEmpty()) {
            String current = queue.remove();
            if (isGoal(current) && !solved) {
                solved = true;
                trace(current);
            }
            explore(current);
        }
        System.out.println(pred.size() + " explored");
    }
}