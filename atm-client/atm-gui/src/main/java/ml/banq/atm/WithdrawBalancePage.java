package ml.banq.atm;

import java.awt.Component;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw balance page
public class WithdrawBalancePage extends Page {
    private static final long serialVersionUID = 1;

    private String accountId;
    private String rfid_uid;
    private String pincode;
    private BanqAPI.Account account;

    public WithdrawBalancePage(String accountId, String rfid_uid, String pincode, BanqAPI.Account account) {
        this.accountId = accountId;
        this.rfid_uid = rfid_uid;
        this.pincode = pincode;
        this.account = account;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_balance_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the amount label
        JLabel amountLabel = new JLabel(String.format("%s %s %.02f", Language.getString("withdraw_balance_page_amount_prefix"), MoneyUtils.getMoneySymbol(), this.account.getAmount()));
        amountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        amountLabel.setFont(Fonts.NORMAL);
        add(amountLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the account menu option label
        JLabel accountLabel = new JLabel("B. " + Language.getString("withdraw_balance_page_account"));
        accountLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        accountLabel.setFont(Fonts.NORMAL);
        add(accountLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the back menu option label
        JLabel backLabel = new JLabel("D. " + Language.getString("withdraw_balance_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // Go back to the account page when the account menu option is selected
        if (key.equals("B")) {
            Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
        }

        // Go back to the account page when the back menu option is selected
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new WithdrawAccountPage(accountId, rfid_uid, pincode, account));
        }
    }
}
