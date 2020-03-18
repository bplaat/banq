package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

public class AdminWriteDonePage extends Page {
    private static final long serialVersionUID = 1;

    public AdminWriteDonePage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel(Language.getString("admin_write_done_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel messageLabel = new JLabel(Language.getString("admin_write_done_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        Navigator.getInstance().changePage(new AdminMenuPage());
    }
}
