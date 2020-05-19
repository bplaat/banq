package ml.banq.atm;

import java.awt.Component;
import java.util.ArrayList;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The Admin write accounts page
public class AdminWriteAccountsPage extends Page {
    private static final long serialVersionUID = 1;

    // A arraylist to hold the user payment accounts
    private ArrayList<BanqAPI.Account> accounts;

    public AdminWriteAccountsPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        // Select all the payment accounts of the logged user
        accounts = BanqAPI.getInstance().getPaymentAccounts();

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("admin_write_accounts_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the menu options for every account
        for (int i = 0; i < accounts.size(); i++) {
            BanqAPI.Account account = accounts.get(i);
            JLabel accountLabel = new JLabel(String.format("%d. %s %s %.2f", i + 1, account.getName(), MoneyUtils.getMoneySymbol(), account.getAmount()));
            accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
            accountLabel.setFont(Fonts.NORMAL);
            add(accountLabel);

            if (i != accounts.size() - 1) {
                add(Box.createVerticalStrut(Paddings.NORMAL));
            }
        }

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the back menu option
        JLabel backLabel = new JLabel("D. " + Language.getString("admin_write_accounts_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When a menu option is selected go the the admin write pincode page with the account id string
        for (int i = 0; i < accounts.size(); i++) {
            if (key.equals(String.valueOf(i + 1))) {
                Navigator.getInstance().changePage(new AdminWritePincodePage(String.format("SU-BANQ-%08d", accounts.get(i).getId())));
            }
        }

        // When the D is pressed logout and go back
        if (key.equals("D")) {
            BanqAPI.getInstance().logout();
            Navigator.getInstance().changePage(new AdminWriteLoginPage());
        }
    }
}
