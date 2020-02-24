package ml.banq.android;

import android.content.Context;
import android.content.Intent;
import android.webkit.JavascriptInterface;

public class WebAppInterface {
    private Context context;

    WebAppInterface(Context context) {
        // We save the context of the activity to call some function which needs the context
        this.context = context;
    }

    // Shows a native share sheed where you can choose an Android app to share the text given
    @JavascriptInterface
    public void share(String text) {
        Intent intent = new Intent();
        intent.setAction(Intent.ACTION_SEND);
        intent.putExtra(Intent.EXTRA_TEXT, text);
        intent.setType("text/plain");
        context.startActivity(Intent.createChooser(intent, null));
    }
}
