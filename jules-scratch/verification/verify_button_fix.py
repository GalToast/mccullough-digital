import os
import re
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Log in to WordPress
    page.goto("http://localhost:8888/wp-login.php")
    page.fill('input[name="log"]', "admin")
    page.fill('input[name="pwd"]', os.environ["WP_APP_PASSWORD"])
    page.click('input[name="wp-submit"]')
    expect(page).to_have_url(re.compile(r".*wp-admin.*"))

    # Go to the homepage editor
    page.goto("http://localhost:8888/wp-admin/post.php?post=2&action=edit")

    # The editor might take a moment to load, so we'll wait for the block to be visible
    button_block = page.frame_locator("iframe[name='editor-canvas']").get_by_text("Start a Project")
    expect(button_block).to_be_visible()
    button_block.click()

    # Verify URL input
    page.frame_locator("iframe[name='editor-canvas']").get_by_role("button", name="Link").click()
    page.frame_locator("iframe[name='editor-canvas']").get_by_placeholder("Paste URL or type to search").fill("https://example.com")
    page.frame_locator("iframe[name='editor-canvas']").get_by_role("button", name="Submit").click()

    # Verify alignment
    page.frame_locator("iframe[name='editor-canvas']").get_by_role("button", name="Align").click()
    page.frame_locator("iframe[name='editor-canvas']").get_by_role("option", name="Align center").click()

    # Update the page
    page.get_by_role("button", name="Update").click()
    # Wait for the "Post updated" message to appear
    expect(page.get_by_text("Home updated.")).to_be_visible()


    # View the page and take a screenshot
    page.goto("http://localhost:8888/")

    button = page.locator(".wp-block-mccullough-digital-button a")
    expect(button).to_have_attribute("href", "https://example.com")
    expect(button.locator("..")).to_have_class(re.compile(r".*aligncenter.*"))

    page.screenshot(path="jules-scratch/verification/verification.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)