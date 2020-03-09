package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class WelcomePage extends Page {
    private static final long serialVersionUID = 1;

    private JLabel messageLabel;

    public WelcomePage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Welcome to Banq");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        messageLabel = new JLabel("Press any key on the keypad to continue...");
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("#")) {
            messageLabel.setText("Hold your bank card by the RFID reader...");
            App.getInstance().writeRFID("SU-BANQ-00000004");
        } else {
            Navigator.changePage(new RFIDPage());
        }
    }

    public void onRFIDWrite(String rfid_uid, String account_id) {
        messageLabel.setText("The account number " + account_id + " has been written to " + rfid_uid);
    }
}
