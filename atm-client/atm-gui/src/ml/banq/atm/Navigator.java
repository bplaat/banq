package ml.banq.atm;

import javax.swing.JScrollPane;

public class Navigator extends JScrollPane {
    private static final long serialVersionUID = 1;

    private static Navigator instance = new Navigator();

    private Navigator() {
        setBorder(null);
    }

    public static Navigator getInstance() {
        return instance;
    }

    public static void changePage(Page page) {
        instance.setViewportView(page);
    }

    public static Page getPage() {
        return (Page)(instance.getViewport().getView());
    }
}
