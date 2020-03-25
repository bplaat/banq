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
        JLabel titleLabel = new JLabel(Language.getString("withdraw_account_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the first menu option label
        JLabel menu1Label = new JLabel("1. " + Language.getString("withdraw_account_page_balance"));
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the second menu option label
        JLabel menu2Label = new JLabel("2. " + Language.getString("withdraw_account_page_withdraw"));
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the back menu option label
        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_account_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // Go to the widthdraw balance page when the first menu option is selected
        if (key.equals("1")) {
            Navigator.getInstance().changePage(new WithdrawBalancePage(accountId, rfid_uid, pincode, account));
        }

        // Go to the withdraw amount page when the second menu option is selected
        if (key.equals("2")) {
            Navigator.getInstance().changePage(new WithdrawAmountPage(accountId, rfid_uid, pincode, account));
        }

        // Go back to the welcome page when the third / back menu option is selected
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WelcomePage());
        }
    }
}
