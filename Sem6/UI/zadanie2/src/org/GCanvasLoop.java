package org;

import java.awt.Canvas;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Frame;
import java.awt.Graphics2D;
import java.awt.Toolkit;
import java.awt.image.BufferStrategy;

import javax.swing.JFrame;

public abstract class GCanvasLoop extends GLoop{
	private JFrame window = new JFrame();
	private Canvas canvas = new Canvas();
	private Color bgcolor = Color.white;
	
	
	public Canvas getCanvas() {
		return canvas;
	}

	public GCanvasLoop(GVector2f size, String title, boolean fullScreen, int fps){
		super(fps);
		
		window.setSize(new Dimension(size.getXi(), size.getYi()));
		window.setTitle(title);
		window.setResizable(true);
		window.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		
		if(fullScreen){
			window.setSize(Toolkit.getDefaultToolkit().getScreenSize());
			window.setExtendedState(Frame.MAXIMIZED_BOTH);
			window.setUndecorated(true);
		}
		canvas.setSize(window.getSize());
		window.add(canvas);
		window.pack();
		window.setVisible(true);
	}
	
	protected final void updateCanvas(){
		BufferStrategy buffer = canvas.getBufferStrategy();
		if(buffer == null){
			canvas.createBufferStrategy(3);
			return;
		}
		Graphics2D g2 = (Graphics2D)buffer.getDrawGraphics();
		clearScreen(g2);
		render(g2);
		g2.dispose();
		buffer.show();
	}
	
	private void clearScreen(Graphics2D g2) {
		g2.setColor(bgcolor);
		g2.fillRect(0, 0, window.getWidth(), window.getHeight());
	}

	@Override
	protected final void loop(float delta) {
		input();
		update(delta);
		updateCanvas();
	}
	
	public final int getWidth(){
		return window.getWidth();
	}
	public final int getHeight(){
		return window.getHeight();
	}
	
	protected abstract void input();
	protected abstract void update(float delta);
	public abstract void render(Graphics2D g2);
}
