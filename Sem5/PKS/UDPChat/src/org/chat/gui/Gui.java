package org.chat.gui;

import java.awt.BorderLayout;
import java.awt.event.WindowEvent;
import java.awt.event.WindowListener;

import javax.swing.JFrame;
import javax.swing.JPanel;

import org.chat.Config;
import org.chat.UDPChat;
import org.chat.utils.Log;

public class Gui extends JFrame{
	private static final long serialVersionUID = 1L;
	
	private WindowListener onWindowChange = new WindowListener(){
		@Override
		public void windowActivated(WindowEvent e) {}

		@Override
		public void windowClosed(WindowEvent e) {}

		@Override
		public void windowClosing(WindowEvent e) {
			if(parent.isConnected())
				parent.stop(true);
		}

		@Override
		public void windowDeactivated(WindowEvent e) {}

		@Override
		public void windowDeiconified(WindowEvent e) {}

		@Override
		public void windowIconified(WindowEvent e) {}

		@Override
		public void windowOpened(WindowEvent e) {}
	};
	
	private UDPChat 	parent;
	private JPanel 		contentPanel	= new JPanel();
	private LoginPanel 	login 			= new LoginPanel(this);
	private ChatPanel  	chat  			= new ChatPanel(this); 
	
	//CONSTRUCTORS
	
	public Gui(UDPChat chat){
		Log.write("zaèal konštruktor objektu Gui", Log.CONSTRUCTORS);
		parent = chat;
		
		setTitle(Config.GUI_TITLE);
		setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		
		addWindowListener(onWindowChange);
		
		contentPanel.setLayout(new BorderLayout());
		add(contentPanel);
		
		
		showLoginView();
		Log.write("skonèil konštruktor objektu Gui", Log.CONSTRUCTORS);
	}
	
	//OTHERS
	
	public void appendText(String txt, boolean recieve){
		chat.appendText((recieve ? parent.getOponentName() : parent.getLogin()) + ": " + txt + "\n");
	}
	
	//SHOWS
	
	public void showChatView(String name){
		contentPanel.removeAll();
		
		contentPanel.add(chat.createLogoutPanel(name), BorderLayout.NORTH);
		contentPanel.add(chat.createMessagePanel(), BorderLayout.SOUTH);
		contentPanel.add(chat.createTextAreaPanel(), BorderLayout.CENTER);
		
		setVisible(true);
		setTitle(Config.GUI_TITLE + ": " + name);
		pack();
	}
	
	public void showLoginView(){
		contentPanel.removeAll();
		
		contentPanel.add(login.createLoginPanel(), BorderLayout.NORTH);
		contentPanel.add(login.createAdressPanel(), BorderLayout.SOUTH);
		contentPanel.add(login.createCheckBoxesPanel(), BorderLayout.CENTER);
		setTitle(Config.GUI_TITLE);
		setVisible(true);
		pack();
	}
	
	//GETTERS
	
	public int getMaxSize(){
		return chat.getMaxSize();
	}

	public UDPChat getChat() {
		return parent;
	}
	
}
