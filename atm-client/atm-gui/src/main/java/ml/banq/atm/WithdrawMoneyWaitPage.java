package ml.banq.atm;

import java.awt.Component;
import java.util.HashMap;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;

// The withdraw money wait page
public class WithdrawMoneyWaitPage extends Page {
    private static final long serialVersionUID = 1;

    private BanqAPI.Transaction transaction;
    private boolean wantReceipt;

    public WithdrawMoneyWaitPage(BanqAPI.Transaction transaction, HashMap<String, Integer> moneyPare, boolean wantReceipt) {
        this.transaction = transaction;
        this.wantReceipt = wantReceipt;

        // Send the money dispencer commands
        App.getInstance().sendMoney(moneyPare);

        // Distract the given money bills from the settings counter
        for (int j = 0; j < Config.ISSUE_AMOUNTS.length; j++) {
            int new_amount = Settings.getInstance().getItem("bills_" + Config.ISSUE_AMOUNTS[j], 0) - moneyPare.get(String.valueOf(Config.ISSUE_AMOUNTS[j]));
            Settings.getInstance().setItem("bills_" + Config.ISSUE_AMOUNTS[j], new_amount);
        }
        Settings.getInstance().save();

        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("withdraw_money_wait_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message
        JLabel messageLabel = new JLabel(Language.getString("withdraw_money_wait_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalGlue());
    }

    // When money is ejected go to printing or done page
    public void onMoney() {
        if (wantReceipt) {
            Navigator.getInstance().changePage(new WithdrawPrintingPage(transaction));
        } else {
            Navigator.getInstance().changePage(new WithdrawDonePage());
        }
    }
}
