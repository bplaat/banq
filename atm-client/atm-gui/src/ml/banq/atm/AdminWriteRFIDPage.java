package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import java.util.ArrayList;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class AdminWriteRFIDPage extends Page {
    private static final long serialVersionUID = 1;

    public AdminWriteRFIDPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Hold your card to the RFID reader");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        JLabel messageLabel = new JLabel("Hold your card to the RFID reader to write: " + BanqAPI.getAccountId());
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());

        App.sendWriteRFID(BanqAPI.getAccountId());
    }

    public void onRFIDWrite(String rfid_uid, String account_id) {
        BanqAPI.setRfidUid(rfid_uid);
        BanqAPI.createCard();
        BanqAPI.logout();

        Navigator.changePage(new AdminWriteDonePage());
    }
}
