package ml.banq.atm;

import java.awt.Component;
import java.util.HashMap;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw receipt page
public class WithdrawReceiptPage extends Page {
    private static final long serialVersionUID = 1;

    private BanqAPI.Transaction transaction;
    private HashMap<String, Integer> moneyPare;

    public WithdrawReceiptPage(BanqAPI.Transaction transaction, HashMap<String, Integer> moneyPare) {
        this.transaction = transaction;
        this.moneyPare = moneyPare;

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_receipt_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        JLabel messageLabel = new JLabel(Language.getString("withdraw_receipt_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page first menu option
        JLabel menu1Label = new JLabel("1. " + Language.getString("withdraw_receipt_page_yes"));
        menu1Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu1Label.setFont(Fonts.NORMAL);
        add(menu1Label);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the page second menu option
        JLabel menu2Label = new JLabel("2. " + Language.getString("withdraw_receipt_page_no"));
        menu2Label.setAlignmentX(Component.CENTER_ALIGNMENT);
        menu2Label.setFont(Fonts.NORMAL);
        add(menu2Label);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When the first option is selected money waint and print receipt
        if (key.equals("1")) {
            Navigator.getInstance().changePage(new WithdrawMoneyWaitPage(transaction, moneyPare, true));
        }

        // Go to the money wait page then to the done page
        if (key.equals("2")) {
            Navigator.getInstance().changePage(new WithdrawMoneyWaitPage(transaction, moneyPare, false));
        }
    }
}
