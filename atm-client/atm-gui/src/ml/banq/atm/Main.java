package ml.banq.atm;

import javax.swing.SwingUtilities;

// The main code entry point
public class Main {
    public static void main(String[] args) {
        // Run the run method of the App singleton in the right Swing thread
        SwingUtilities.invokeLater(App.getInstance());
    }
}
