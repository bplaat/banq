package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw account page
public class WithdrawAccountPage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;

    public WithdrawAccountPage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_account_page_title_prefix") + " " + account.getName());
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the page amount line
        JLabel amountLabel = new JLabel(String.format("\u20bd %.2f", account.getAmount()));
        amountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        amountLabel.setFont(Fonts.NORMAL);
        add(amountLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the first menu option label
        JLabel menu1Label = new JLabel("1. " + Language.getString("withdraw_account_page_withdraw"));
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the second menu option label
        JLabel menu2Label = new JLabel("D. " + Language.getString("withdraw_account_page_back"));
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // Go to the withdraw amount page when the first menu option is selected
        if (key.equals("1")) {
            Navigator.getInstance().changePage(new WithdrawAmountPage(accountId, rfid_uid, pincode, account));
        }

        // Go back to the welcome page when the second / back menu option is selected
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WelcomePage());
        }
    }
}
