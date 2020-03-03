# A simple build script witch build the java files in the src folder to a linked jar file
mkdir classes
if javac -Xlint -cp jSerialComm-2.6.0.jar -d classes $(find src -name *.java); then
    jar cfm keypad.jar src/manifest.mf -C classes .
    java -jar keypad.jar
fi
rm -r classes
