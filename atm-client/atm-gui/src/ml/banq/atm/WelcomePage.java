package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class WelcomePage extends Page {
    private static final long serialVersionUID = 1;

    public WelcomePage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Welcome to Banq");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        JLabel messageLabel = new JLabel("Press any key on the keypad to continue...");
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        Navigator.changePage(new RFIDPage());
    }
}
