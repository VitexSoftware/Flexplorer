# Selenium tests

Run against the public AbraFlexi demo (`winstrom`/`winstrom`, company `demo`).

    cd src && php -S localhost:8080 &
    pip install selenium pytest
    pytest tests/selenium

Set `FLEXPLORER_URL`, `ABRAFLEXI_SERVER`, `ABRAFLEXI_LOGIN`, `ABRAFLEXI_PASSWORD`, `ABRAFLEXI_COMPANY` to override; `HEADLESS=0` shows the browser.
