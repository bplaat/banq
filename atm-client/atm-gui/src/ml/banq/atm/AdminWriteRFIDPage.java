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

    private String accountId;
    private String pincode;

    public AdminWriteRFIDPage(String accountId, String pincode) {
        this.accountId = accountId;
        this.pincode = pincode;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Hold your card to the RFID reader");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel messageLabel = new JLabel("Hold your card to the RFID reader to write: " + accountId);
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());

        App.getInstance().sendWriteRFID(accountId);
    }

    public void onRFIDWrite(String account_id, String rfid_uid) {
        BanqAPI.getInstance().createCard(account_id, rfid_uid, pincode);
        BanqAPI.getInstance().logout();

        Navigator.getInstance().changePage(new AdminWriteDonePage());
    }
}
