"""Flexplorer UI tests against the AbraFlexi demo company (winstrom/winstrom)."""
import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC

from conftest import BASE, COMPANY, SERVER, do_login, page_errors


class TestAnonymous:
    def test_login_form_fields(self, driver):
        driver.get(f"{BASE}/login.php")
        for name in ("server", "login", "password"):
            assert driver.find_element(By.NAME, name)
        assert driver.find_element(By.ID, "signin")
        assert not page_errors(driver)

    def test_role_permissions_public(self, driver):
        driver.get(f"{BASE}/permissions.php")
        assert not page_errors(driver)

    def test_protected_page_redirects_to_login(self, driver):
        driver.get(f"{BASE}/evidences.php")
        assert "login.php" in driver.current_url or driver.find_elements(By.NAME, "password")

    def test_wrong_password_stays_on_login(self, driver):
        driver.get(f"{BASE}/login.php")
        for name, value in (("server", SERVER), ("login", "winstrom"), ("password", "definitely-wrong")):
            f = driver.find_element(By.NAME, name)
            f.clear()
            f.send_keys(value)
        driver.find_element(By.ID, "signin").click()
        assert "companies.php" not in driver.current_url
        assert driver.find_elements(By.NAME, "password")


class TestLoggedIn:
    def test_login_lands_on_companies(self, driver, wait):
        do_login(driver, wait)
        assert "companies.php" in driver.current_url
        assert not page_errors(driver)

    def test_demo_company_listed(self, logged_in):
        logged_in.get(f"{BASE}/companies.php")
        assert COMPANY in logged_in.page_source

    def test_navbar_menus(self, logged_in):
        logged_in.get(f"{BASE}/companies.php")
        nav = logged_in.find_element(By.ID, "header").text
        for label in ("Company", "Evidence", "Tools"):
            assert label in nav
        assert logged_in.find_elements(By.CSS_SELECTOR, "#header form input[type=search], #header form input[type=text]")

    def test_company_page(self, logged_in):
        logged_in.get(f"{BASE}/company.php?company={COMPANY}")
        assert not page_errors(logged_in)

    def test_evidences_overview(self, logged_in):
        logged_in.get(f"{BASE}/evidences.php")
        assert logged_in.find_element(By.ID, "EvidenceTabs")
        assert "adresar" in logged_in.page_source
        assert not page_errors(logged_in)

    def test_last_url_panel(self, logged_in, wait):
        logged_in.get(f"{BASE}/evidences.php")
        wait.until(lambda d: "query.php?url=" in (d.find_element(By.CSS_SELECTOR, "#lasturl a").get_attribute("href") or ""))

    @pytest.mark.parametrize("evidence", ["adresar", "faktura-vydana", "cenik"])
    def test_evidence_page(self, logged_in, evidence):
        logged_in.get(f"{BASE}/evidence.php?evidence={evidence}")
        assert evidence in logged_in.page_source
        assert not page_errors(logged_in)

    def test_evidence_datagrid_loads_rows(self, logged_in, wait):
        logged_in.get(f"{BASE}/evidence.php?evidence=adresar")
        wait.until(lambda d: d.find_elements(By.CSS_SELECTOR, "table tbody tr"))

    @pytest.mark.parametrize("page", [
        "query.php", "buttons.php", "changesapi.php", "changes.php",
        "fakechange.php", "ucetniobdobi.php", "backups.php", "settings.php", "about.php",
    ])
    def test_tool_pages_render(self, logged_in, page):
        logged_in.get(f"{BASE}/{page}")
        assert "login.php" not in logged_in.current_url
        assert not page_errors(logged_in)

    def test_search(self, logged_in):
        logged_in.get(f"{BASE}/searcher.php?q=a")
        assert not page_errors(logged_in)

    def test_theme_applied(self, logged_in):
        logged_in.get(f"{BASE}/evidences.php")
        assert logged_in.execute_script("return document.documentElement.getAttribute('data-theme')") == "dark"
        bg = logged_in.execute_script("return getComputedStyle(document.body).backgroundColor")
        assert bg == "rgb(29, 20, 64)"

    def test_logout(self, logged_in, wait):
        logged_in.get(f"{BASE}/logout.php")
        logged_in.get(f"{BASE}/evidences.php")
        assert logged_in.find_elements(By.NAME, "password")
