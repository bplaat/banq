package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The admin foreign write done page
public class AdminWriteForeignDonePage extends Page {
    private static final long serialVersionUID = 1;

    public AdminWriteForeignDonePage(String rfid_uid) {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("admin_write_foreign_done_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        JLabel messageLabel = new JLabel(Language.getString("admin_write_foreign_done_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page rfid label
        JLabel rfidLabel = new JLabel(Language.getString("admin_write_foreign_done_page_rfid_prefix") + " " + rfid_uid);
        rfidLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        rfidLabel.setFont(Fonts.NORMAL);
        add(rfidLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // Go to the admin menu page when a key is pressed
        Navigator.getInstance().changePage(new AdminMenuPage());
    }
}
