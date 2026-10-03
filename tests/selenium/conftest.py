"""Selenium fixtures for Flexplorer.

Environment:
  FLEXPLORER_URL       base URL of the running app (default http://localhost:8080)
  ABRAFLEXI_SERVER     AbraFlexi server (default https://demo.flexibee.eu:5434)
  ABRAFLEXI_LOGIN      default winstrom
  ABRAFLEXI_PASSWORD   default winstrom
  ABRAFLEXI_COMPANY    default demo
  HEADLESS             "0" to watch the browser
"""
import os
import shutil

import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.firefox.options import Options
from selenium.webdriver.firefox.service import Service
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait

BASE = os.environ.get("FLEXPLORER_URL", "http://localhost:8080").rstrip("/")
SERVER = os.environ.get("ABRAFLEXI_SERVER", "https://demo.flexibee.eu:5434")
LOGIN = os.environ.get("ABRAFLEXI_LOGIN", "winstrom")
PASSWORD = os.environ.get("ABRAFLEXI_PASSWORD", "winstrom")
COMPANY = os.environ.get("ABRAFLEXI_COMPANY", "demo")


@pytest.fixture(scope="session")
def base_url():
    return BASE


@pytest.fixture(scope="session")
def driver():
    opts = Options()
    # evidence pages embed iframes served straight from AbraFlexi, which ask for Basic auth
    opts.set_capability("unhandledPromptBehavior", "dismiss")
    if os.environ.get("HEADLESS", "1") != "0":
        opts.add_argument("-headless")
    gecko = shutil.which("geckodriver")
    drv = webdriver.Firefox(options=opts, service=Service(gecko) if gecko else None)
    drv.set_window_size(1400, 1000)
    drv.implicitly_wait(2)
    yield drv
    drv.quit()


@pytest.fixture(scope="session")
def wait(driver):
    return WebDriverWait(driver, 30)


def do_login(driver, wait):
    driver.get(f"{BASE}/login.php")
    for name, value in (("server", SERVER), ("login", LOGIN), ("password", PASSWORD)):
        field = driver.find_element(By.NAME, name)
        field.clear()
        field.send_keys(value)
    driver.find_element(By.ID, "signin").click()
    wait.until(EC.url_contains("companies.php"))


@pytest.fixture(scope="session")
def logged_in(driver, wait):
    do_login(driver, wait)
    return driver


def page_errors(driver):
    """PHP errors that leak into the page body."""
    text = driver.find_element(By.TAG_NAME, "body").text
    return [m for m in ("Fatal error", "Parse error", "Uncaught", "Warning:", "Notice:", "Deprecated:") if m in text]
