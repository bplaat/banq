package ml.banq.atm;

import java.util.ArrayList;
import java.util.HashMap;

// The static money utils class
public class MoneyUtils {
    private MoneyUtils() {}

    // Get ruble money symbol if the font support it
    public static String getMoneySymbol() {
        return Fonts.DEFAULT.canDisplay('\u20bd') ? "\u20bd" : "P";
    }

    // A function the generates the diffrent money pares
    public static ArrayList<HashMap<String, Integer>> getMoneyPares(int amount) {
        ArrayList<HashMap<String, Integer>> moneyPares = new ArrayList<HashMap<String, Integer>>();

        int valuesCount = Config.ISSUE_AMOUNTS.length;

        // Create rounds
        int[][] rounds = new int[valuesCount][valuesCount];
        for (int i = 0; i < valuesCount; i++) {
            int[] newRound = new int[valuesCount];
            for (int j = 0; j < valuesCount; j++) {
                newRound[(i + j) % valuesCount] = Config.ISSUE_AMOUNTS[j];
            }
            rounds[i] = newRound;
        }

        // Run diffrent rounds
        for (int[] round : rounds) {
            int new_amount = amount;
            HashMap<String, Integer> moneyPare = new HashMap<String, Integer>();
            for (int i = valuesCount - 1; i >= 0; i--) {
                int count = 0;
                while (new_amount >= round[i]) {
                    new_amount -= round[i];
                    count++;
                }
                moneyPare.put(String.valueOf(round[i]), count);
            }
            moneyPares.add(moneyPare);
        }

        return moneyPares;
    }
}
