package ml.banq.atm;

import java.awt.Component;
import java.awt.Font;
import java.util.ArrayList;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class AdminWriteAccountsPage extends Page {
    private static final long serialVersionUID = 1;

    private ArrayList<BanqAPI.Account> accounts;

    public AdminWriteAccountsPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        accounts = BanqAPI.getInstance().getPaymentAccounts();

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Select a Banq account to add your card to");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        for (int i = 0; i < accounts.size(); i++) {
            BanqAPI.Account account = accounts.get(i);
            JLabel accountLabel = new JLabel(String.format("%d. %s \u20ac %.2f", i + 1, account.getName(), account.getAmount()));
            accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
            accountLabel.setFont(Fonts.NORMAL);
            add(accountLabel);

            if (i != accounts.size() - 1) {
                add(Box.createVerticalStrut(Paddings.NORMAL));
            }
        }

        add(Box.createVerticalStrut(Paddings.LARGE));

        JLabel backLabel = new JLabel("Press the 'D' key to logout and go back the login page");
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        if (key.equals("D")) {
            BanqAPI.getInstance().logout();
            Navigator.getInstance().changePage(new AdminWriteLoginPage());
        }

        for (int i = 0; i < accounts.size(); i++) {
            if (key.equals(String.valueOf(i + 1))) {
                Navigator.getInstance().changePage(new AdminWritePincodePage(String.format("SU-BANQ-%08d", accounts.get(i).getId())));
            }
        }
    }
}
