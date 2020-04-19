package ml.banq.atm;

import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.FlowLayout;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;

// The admin write login page
public class AdminWriteLoginPage extends Page {
    private static final long serialVersionUID = 1;

    public AdminWriteLoginPage() {
        setLayout(new BoxLayout(this, BoxLayout.PAGE_AXIS));

        add(Box.createVerticalGlue());

        // Create the page title
        JLabel titleLabel = new JLabel(Language.getString("admin_write_login_page_title"));
        titleLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        titleLabel.setFont(Fonts.HEADER);
        add(titleLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page message label
        final JLabel messageLabel = new JLabel(Language.getString("admin_write_login_page_message"));
        messageLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        messageLabel.setFont(Fonts.NORMAL);
        add(messageLabel);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page login input label
        JLabel loginLabel = new JLabel(Language.getString("admin_write_login_page_login_input"));
        loginLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        loginLabel.setFont(Fonts.NORMAL);
        add(loginLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the page login input field
        final JTextField loginInput = new JTextField(16);
        loginInput.setFont(Fonts.NORMAL);
        loginInput.setHorizontalAlignment(JTextField.CENTER);
        loginInput.setMaximumSize(loginInput.getPreferredSize());
        add(loginInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the page password input label
        JLabel passwordLabel = new JLabel(Language.getString("admin_write_login_page_password_input"));
        passwordLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        passwordLabel.setFont(Fonts.NORMAL);
        add(passwordLabel);

        add(Box.createVerticalStrut(Paddings.NORMAL));

        // Create the page password input field
        final JPasswordField passwordInput = new JPasswordField(16);
        passwordInput.setFont(Fonts.NORMAL);
        passwordInput.setHorizontalAlignment(JPasswordField.CENTER);
        passwordInput.setMaximumSize(passwordInput.getPreferredSize());
        add(passwordInput);

        add(Box.createVerticalStrut(Paddings.LARGE));

        // Create the buttons box
        JPanel buttonsBox = new JPanel(new FlowLayout(FlowLayout.CENTER, Paddings.NORMAL, 0));
        buttonsBox.setMaximumSize(new Dimension(App.getInstance().getWindowWidth() / 2, 0));
        add(buttonsBox);

        // Create the login button
        JButton loginButton = new JButton(Language.getString("admin_write_login_page_login_button"));
        loginButton.setFont(Fonts.NORMAL);
        loginButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                // Try to login
                if (BanqAPI.getInstance().login(loginInput.getText(), new String(passwordInput.getPassword()))) {
                    App.getInstance().sendBeeper(880, 250);
                    Navigator.getInstance().changePage(new AdminWriteAccountsPage(), false);
                } else {
                    App.getInstance().sendBeeper(110, 250);
                    messageLabel.setText(Language.getString("admin_write_login_page_error"));
                }
            }
        });
        buttonsBox.add(loginButton);

        // Create the back button
        JButton backButton = new JButton(Language.getString("admin_write_login_page_back_button"));
        backButton.setAlignmentX(Component.CENTER_ALIGNMENT);
        backButton.setFont(Fonts.NORMAL);
        backButton.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent event) {
                // When pressed go back
                Navigator.getInstance().changePage(new AdminMenuPage());
            }
        });
        buttonsBox.add(backButton);

        add(Box.createVerticalGlue());
    }

    public void onKeypad(String key) {
        // When pressed go back to the previous page
        if (key.equals("D")) {
            Navigator.getInstance().changePage(new AdminMenuPage());
        }
    }
}
