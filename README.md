# Text
Profane text classification.

# How to Use
To use the text classification, you can either make a `GET` or a `POST` request to `https://kks.zanderlewis.dev/text/detect.php`
Use the following as a guide:

```
whitelisted => words,or phrases to whitelist
blacklisted => words,or phrases to blacklist
input => the input string to check
```

These can be used like so:

```
(https://kks.zanderlewis.dev/text/detect.php?whitelisted=words,or%20phrases%20here)
(https://kks.zanderlewis.dev/text/detect.php?blacklisted=words,or%20phrase%20here)
(https://kks.zanderlewis.dev/text/detect.php?input=some%20string%20here)
```

**NOTE:**
Whitelisted words/phrases have priority. If a word is in whitelist and blacklist, it will be whitelisted.

<br>

**NOTE:**
Please note that there are no spaces after the commas.
