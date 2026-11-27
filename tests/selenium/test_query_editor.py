"""Browser checks for the request body editor without an AbraFlexi connection."""
import subprocess
import json
from pathlib import Path
from urllib.parse import quote

from selenium.webdriver import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.webdriver.support.ui import WebDriverWait


ROOT = Path(__file__).resolve().parents[2]


def test_query_body_editor(driver):
    body = '</textarea><button>+%20 &amp;</button>'
    php = (
        'require "vendor/autoload.php"; new Flexplorer\\ui\\WebPage(); '
        'echo new Flexplorer\\ui\\SendForm("https://example.test/record.json", '
        '"PUT", $argv[1], "json");'
    )
    form = subprocess.run(
        ["php", "-r", php, body], cwd=ROOT, check=True, capture_output=True, text=True
    ).stdout
    driver.get("data:text/html;charset=utf-8," + quote(form))
    for path in ("src/css/bootstrap.min.css", "src/css/vitex.css", "src/css/query-editor.css"):
        driver.execute_script(
            "const s = document.createElement('style'); s.textContent = arguments[0]; "
            "document.head.appendChild(s);", (ROOT / path).read_text()
        )
    for path in ("src/js/highlight.min.js", "src/js/query-editor.js"):
        driver.execute_script((ROOT / path).read_text())

    textarea = driver.find_element(By.CSS_SELECTOR, "textarea[name=body]")
    highlight = driver.find_element(By.CSS_SELECTOR, ".query-body-highlight")
    format_select = Select(driver.find_element(By.NAME, "format"))
    assert textarea.get_attribute("value") == body
    assert not driver.find_elements(By.CSS_SELECTOR, "form button:not([class])")
    assert textarea.value_of_css_property("resize") == "vertical"
    initial_height = textarea.size["height"]
    ActionChains(driver).move_to_element_with_offset(
        textarea, textarea.size["width"] / 2 - 5, textarea.size["height"] / 2 - 5
    ).click_and_hold().move_by_offset(0, 100).release().perform()
    assert textarea.size["height"] > initial_height

    json = '{"url":"https://example.test/?a=1+2%20","id":180}'
    driver.execute_script(
        "arguments[0].value = arguments[1]; arguments[0].dispatchEvent(new Event('input'));",
        textarea, json
    )
    assert highlight.text == json
    assert highlight.find_elements(By.CSS_SELECTOR, ".hljs-attr")
    assert driver.execute_script("return new FormData(arguments[0].form).get('body')", textarea) == json

    long_json = '{"text":"' + "a" * 1000 + '"}\n' * 100
    driver.execute_script(
        "arguments[0].value = arguments[1]; arguments[0].dispatchEvent(new Event('input'));"
        "arguments[0].scrollTop = 500; arguments[0].scrollLeft = 100;"
        "arguments[0].dispatchEvent(new Event('scroll'));", textarea, long_json
    )
    assert driver.execute_script(
        "return arguments[0].scrollTop > 0 && arguments[0].scrollLeft > 0 && "
        "arguments[0].scrollTop === arguments[1].scrollTop && "
        "arguments[0].scrollLeft === arguments[1].scrollLeft", textarea, highlight
    )

    format_select.select_by_value("xml")
    xml = '<winstrom><url>a+b &amp; c</url></winstrom>'
    driver.execute_script(
        "arguments[0].value = arguments[1]; arguments[0].dispatchEvent(new Event('change'));",
        textarea, xml
    )
    formatted_xml = textarea.get_attribute("value")
    assert "\n" in formatted_xml
    assert "<url>a+b &amp; c</url>" in formatted_xml
    assert highlight.text == formatted_xml
    assert highlight.find_elements(By.CSS_SELECTOR, ".hljs-name")

    format_select.select_by_value("csv")
    assert highlight.text == formatted_xml
    assert not highlight.find_elements(By.TAG_NAME, "span")

    format_select.select_by_value("xml")
    mixed = '<winstrom><body xml:space="preserve"> before <tag/> after </body></winstrom>'
    driver.execute_script(
        "arguments[0].value = arguments[1]; arguments[0].dispatchEvent(new Event('change'));",
        textarea, mixed
    )
    assert ' before <tag/> after ' in textarea.get_attribute("value")


def test_evidence_property_selection_and_format_conversion(driver):
    body = '{"winstrom":{"@version":"1.0","custom-button":[{"id":"180","title":"One","url":"a+b%20"},{"id":181,"title":"Two"}]}}'
    php = (
        'require "vendor/autoload.php"; new Flexplorer\\ui\\WebPage(); '
        'echo new Flexplorer\\ui\\SendForm("https://example.test/c/demo/custom-button/180.json", '
        '"PUT", $argv[1], "json");'
    )
    form = subprocess.run(
        ["php", "-r", php, body], cwd=ROOT, check=True, capture_output=True, text=True
    ).stdout
    metadata = subprocess.run(
        ["php", "-r", 'require "vendor/autoload.php"; echo json_encode(array_values(AbraFlexi\\Functions::getOfflineColumnsInfo("custom-button")));'],
        cwd=ROOT, check=True, capture_output=True, text=True
    ).stdout
    properties = [
        {"name": item["propertyName"], "label": item["name"], "type": item["type"]}
        for item in json.loads(metadata)
    ]
    driver.get("data:text/html;charset=utf-8," + quote(form))
    driver.execute_script(
        "window.fetch = () => Promise.resolve({ok: true, json: () => Promise.resolve(arguments[0])});",
        properties
    )
    for path in ("src/js/highlight.min.js", "src/js/query-editor.js", "src/js/query-properties.js"):
        driver.execute_script((ROOT / path).read_text())

    wait = WebDriverWait(driver, 10)
    wait.until(lambda d: len(d.find_elements(By.CSS_SELECTOR, ".query-property-pill")) > 0)
    textarea = driver.find_element(By.CSS_SELECTOR, "textarea[name=body]")
    assert "\n" in textarea.get_attribute("value")
    selector = Select(driver.find_element(By.CSS_SELECTOR, ".query-record"))
    assert len(selector.options) == 2
    selector.select_by_value("1")
    title = next(button for button in driver.find_elements(By.CSS_SELECTOR, ".query-property-pill") if button.text.startswith("title "))
    assert title.get_attribute("aria-pressed") == "true"
    title.click()
    parsed = json.loads(textarea.get_attribute("value"))
    assert parsed["winstrom"]["custom-button"][0]["title"] == "One"
    assert "title" not in parsed["winstrom"]["custom-button"][1]

    url = next(button for button in driver.find_elements(By.CSS_SELECTOR, ".query-property-pill") if button.text.startswith("url "))
    url.click()
    parsed = json.loads(textarea.get_attribute("value"))
    assert parsed["winstrom"]["custom-button"][1]["url"] == ""

    format_select = Select(driver.find_element(By.NAME, "format"))
    format_select.select_by_value("xml")
    assert "<custom-button>" in textarea.get_attribute("value")
    assert driver.find_element(By.NAME, "url").get_attribute("value").endswith(".xml")
    assert len(Select(driver.find_element(By.CSS_SELECTOR, ".query-record")).options) == 2
    title = next(button for button in driver.find_elements(By.CSS_SELECTOR, ".query-property-pill") if button.text.startswith("title "))
    assert title.get_attribute("aria-pressed") == "false"
    title.click()
    assert "<title/>" in textarea.get_attribute("value")
    format_select.select_by_value("json")
    parsed = json.loads(textarea.get_attribute("value"))
    assert parsed["winstrom"]["custom-button"][0]["url"] == "a+b%20"
    assert parsed["winstrom"]["custom-button"][1]["url"] == ""
    assert parsed["winstrom"]["custom-button"][1]["id"] == 181
    assert parsed["winstrom"]["custom-button"][1]["title"] == ""

    driver.execute_script(
        "arguments[0].value = arguments[1]; arguments[0].dispatchEvent(new Event('input'));",
        textarea, '{"winstrom":{"custom-button":[{"nested":[{"id":"1"}]}]}}'
    )
    original = textarea.get_attribute("value")
    format_select.select_by_value("xml")
    assert format_select.first_selected_option.get_attribute("value") == "json"
    assert textarea.get_attribute("value") == original
    assert driver.find_element(By.CSS_SELECTOR, ".query-properties-message").text

    driver.execute_script(
        "arguments[0].value = arguments[1]; arguments[0].dispatchEvent(new Event('change'));",
        textarea, '{"winstrom":{"custom-button":[{"id":9007199254740993}]}}'
    )
    assert "9007199254740993" in textarea.get_attribute("value")
    assert driver.find_element(By.CSS_SELECTOR, ".query-properties-message").text
