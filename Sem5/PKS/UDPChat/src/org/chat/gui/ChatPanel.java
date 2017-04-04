package org.chat.gui;

import java.awt.BorderLayout;
import java.awt.Dimension;
import java.awt.Panel;

import javax.swing.JButton;
import javax.swing.JFileChooser;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JSpinner;
import javax.swing.JTextArea;
import javax.swing.SpinnerNumberModel;

import org.chat.Config;
import org.chat.core.Server;
import org.chat.utils.Log;

public class ChatPanel extends Panel{
	private static final long serialVersionUID = 1L;

	private Gui 			parent;
	private JTextArea 		chatHistory = new JTextArea(10,25);
	private JTextArea 		message 	= new JTextArea(2, 19);
	private JButton 		sentMessage = new JButton("Send");
	private JButton 		logout 		= new JButton("Logout");
	private JButton 		loadImage	= new JButton("send file");
	private JFileChooser 	fileChooser = new JFileChooser();
	private JSpinner 		maxSize		= new JSpinner(new SpinnerNumberModel(Config.CHAT_DEFAULT_MSG_SIZE,
			  																  1,
			  																  Config.CHAT_TOTAL_MAX_MSG_SIZE,
			  																  10));
	
	//CONSTRUCTORS
	
	public ChatPanel(Gui parent){
		Log.write("zaèal konštruktor objektu ChatPanel", Log.CONSTRUCTORS);
		this.parent = parent;
		Log.write("skonèil konštruktor objektu ChatPanel", Log.CONSTRUCTORS);
	}
	
	//OTHERS
	
	public void appendText(String txt){
		chatHistory.append(txt);
	}
	
	//CREATORS
	

	JPanel createTextAreaPanel(){
		JPanel panel = new JPanel();
		
		chatHistory.setLineWrap(true);
		chatHistory.setEditable(false);
		
		JScrollPane scroll = new JScrollPane(chatHistory);
		
		scroll.setHorizontalScrollBarPolicy(JScrollPane.HORIZONTAL_SCROLLBAR_NEVER);
		scroll.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_AS_NEEDED);
		panel.add(scroll);

		return panel;
	}
	
	JPanel createMessagePanel(){
		JPanel panel = new JPanel();
		
		message.setLineWrap(true);
		
		JScrollPane scroll = new JScrollPane(message);
		
		scroll.setHorizontalScrollBarPolicy(JScrollPane.HORIZONTAL_SCROLLBAR_NEVER);
		scroll.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_AS_NEEDED);
		panel.add(scroll);
		
//		panel.add(message);
		panel.add(sentMessage);
		
		sentMessage.addActionListener(a -> {
			parent.getChat().sendMessage(message.getText(), Server.CLIENT_SEND_MSG);
			message.setText("");
		});

		
		return panel;
	}
	
	JPanel createLogoutPanel(String name){
		JPanel panel = new JPanel();
		panel.setLayout(new BorderLayout());
		
		
		JPanel p = new JPanel();
		p.add(new JLabel("message size: "));
		maxSize.getEditor().setPreferredSize(new Dimension(20,20));
		p.add(maxSize);
		panel.add(p,BorderLayout.CENTER);
		loadImage.addActionListener(a -> {
			fileChooser.showOpenDialog(null);
			
			if(fileChooser.getSelectedFile() != null)
				parent.getChat().sendFile(fileChooser.getSelectedFile());
		});
		panel.add(loadImage, BorderLayout.WEST);
		panel.add(logout, BorderLayout.EAST);
		logout.addActionListener(a -> parent.getChat().stop(true));
		
		return panel;
	}
	
	//GETTERS
	
	int getMaxSize(){
		if(maxSize == null)
			return Config.CHAT_TOTAL_MAX_MSG_SIZE;
		return (int)maxSize.getValue();
	}
	
}
