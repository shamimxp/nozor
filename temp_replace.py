import sys

filename = "resources/views/frontend/page/shop.blade.php"
with open(filename, "r") as f:
    lines = f.readlines()

# Verify we're removing the correct block
if 'class="row product-grid"' not in lines[50]:
    print("Error: Line 51 does not match expected start. Found:", lines[50].strip())
    sys.exit(1)

if '</div>' not in lines[945]:
    print("Error: Line 946 does not match expected end. Found:", lines[945].strip())
    sys.exit(1)

new_lines = lines[:50] + [
    '                <div class="row product-grid" id="product-list-container">\n',
    "                    @include('frontend.partials.shop_products')\n",
    '                </div>\n'
] + lines[946:]

with open(filename, "w") as f:
    f.writelines(new_lines)

print("Replaced lines 51-946 successfully.")
