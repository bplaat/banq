package ml.banq.atm;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTextField;

// The admin bills page
public class AdminBillsPage extends Page {
    private static final long serialVersionUID = 1;

    private JTextField[] billInputs;

    public AdminBillsPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("admin_bills_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        JLabel messageLabel = new JLabel(Language.getString("admin_bills_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the bill inputs
        billInputs = new JTextField[Config.ISSUE_AMOUNTS.length];

        for (int i = 0; i < Config.ISSUE_AMOUNTS.length; i++) {
            // Create the bill input box
            JPanel billBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
            billBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
            add(billBox);

            // Create the bill input label
            JLabel billLabel = new JLabel(MoneyUtils.getMoneySymbol() + " " + Config.ISSUE_AMOUNTS[i] + " = ");
            billLabel.setFont(Fonts.NORMAL);
            billBox.add(billLabel);

            // Create the bill input field
            billInputs[i] = new JTextField(8);
            billInputs[i].setText(String.valueOf(Settings.getInstance().getItem("bills_" + Config.ISSUE_AMOUNTS[i], 0)));
            billInputs[i].setFont(Fonts.NORMAL);
            billInputs[i].setHorizontalAlignment(JTextField.CENTER);
            billBox.add(billInputs[i]);

            if (i != Config.ISSUE_AMOUNTS.length - 1) {
                add(Box.createVerticalStrut(Paddings.NORMAL));
            }
        }

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the back menu option
        JLabel backLabel = new JLabel("D. " + Language.getString("admin_bills_page_back"));
        backLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        backLabel.setFont(Fonts.NORMAL);
        add(backLabel);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When pressed save and go back to the previous page
        if (key.equals("D")) {
            try {
                // Save the bill amounts
                for (int i = 0; i < Config.ISSUE_AMOUNTS.length; i++) {
                    // Check if they are numbers and not negative
                    int amount = Integer.parseInt(billInputs[i].getText());
                    if (amount < 0) {
                        throw new NumberFormatException("Negative bills amount");
                    }

                    Settings.getInstance().setItem("bills_" + Config.ISSUE_AMOUNTS[i], amount);
                }
                Settings.getInstance().save();

                Navigator.getInstance().changePage(new AdminMenuPage());
            } catch (Exception exception) {
                Log.error(exception);
            }
        }
    }
}
