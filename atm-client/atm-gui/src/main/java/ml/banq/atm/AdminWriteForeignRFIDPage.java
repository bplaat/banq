package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The admin write foreign RFID page
public class AdminWriteForeignRFIDPage extends Page {
    private static final long serialVersionUID = 1;

    public AdminWriteForeignRFIDPage(String accountId) {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title page
        JLabel titleLabel = new JLabel(Language.getString("admin_write_foreign_rfid_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        JLabel messageLabel = new JLabel(Language.getString("admin_write_foreign_rfid_page_message_prefix") + " " + accountId);
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());

        // Init writing to the card
        App.getInstance().sendWriteRFID(accountId);
    }

    public void onRFIDWrite(String account_id, String rfid_uid) {
        // Go to the done page
        App.getInstance().sendBeeper(880, 250);
        Navigator.getInstance().changePage(new AdminWriteForeignDonePage(rfid_uid), false);
    }
}
