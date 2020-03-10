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

        accounts = BanqAPI.getPaymentAccounts();

        add(Box.createVerticalGlue());

        JLabel titleLabel = new JLabel("Select a Banq account to add your card to");
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(24));

        for (int i = 0; i < accounts.size(); i++) {
            BanqAPI.Account account = accounts.get(i);
            JLabel accountLabel = new JLabel(String.format("%d. %s %.2f", i + 1, account.getName(), account.getAmount()));
            accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
            accountLabel.setFont(Fonts.NORMAL);
            add(accountLabel);

            if (i != accounts.size() - 1) {
                add(Box.createVerticalStrut(16));
            }
        }

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        for (int i = 0; i < accounts.size(); i++) {
            BanqAPI.Account account = accounts.get(i);
            if (Integer.parseInt(key) == i + 1) {
                String account_id = String.format("SU-BANQ-%08d", account.getId());
                BanqAPI.setAccountId(account_id);
                Navigator.changePage(new AdminWritePincodePage());
            }
        }
    }
}
