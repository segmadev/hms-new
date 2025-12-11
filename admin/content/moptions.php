<style>
.custom-multiselect-wrapper {
    background-color: transparent;
}

.custom-multiselect-wrapper {
    position: relative;
    width: 100%;
}

.custom-multiselect-display {
    border: 1px solid #ced4da;
    border-radius: 4px;
    min-height: 38px;
    padding: 4px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    cursor: pointer;
    /* background-color: #263240; */
}

/* .custom-multiselect-display .placeholder {
    color: #6c757d;
} */

.custom-multiselect-display .tag {
    background-color: #007bff;
    color: #fff;
    padding: 2px 8px;
    margin: 2px;
    border-radius: 12px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    max-width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.custom-multiselect-display .tag .remove-tag {
    margin-left: 8px;
    cursor: pointer;
    font-weight: bold;
}

.custom-multiselect-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    /* border: 1px solid #ced4da; */
    border-radius: 4px;
    background-color: #fff;
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    text-align: left;
    color: black;
}

.custom-multiselect-dropdown input {
    width: 100%;
    padding: 8px;
    border: none;
    outline: none;
    box-sizing: border-box;
}

.custom-multiselect-dropdown label {
    display: block;
    padding: 4px 8px;
    cursor: pointer;
    text-align: left;
    color: black;
}

.custom-multiselect-dropdown label:hover {
    background-color: #f8f9fa;
}
</style>


<script>
(function() {
    function setupCustomMultiselect(select) {
        // const placeholder = select.options[0].text || select.dataset.placeholder || "";
        const placeholder = "";
        const wrapper = document.createElement("div");
        wrapper.classList.add("custom-multiselect-wrapper");

        const display = document.createElement("div");
        display.classList.add("custom-multiselect-display", "form-control");
        const placeholderSpan = document.createElement("span");
        placeholderSpan.textContent = placeholder;
        display.appendChild(placeholderSpan);
        wrapper.appendChild(display);

        const dropdown = document.createElement("div");
        dropdown.classList.add("custom-multiselect-dropdown");
        // dropdown.classList.add("bg-body");
        // dropdown.classList.add("text-body");
        wrapper.appendChild(dropdown);

        // Search box
        const searchInput = document.createElement("input");
        searchInput.type = "text";
        searchInput.placeholder = "Search...";
        dropdown.appendChild(searchInput);

        // Options and pre-selected items
        Array.from(select.options).forEach((option) => {
            if (!option.value) return;

            const item = document.createElement("label");
            item.innerHTML = `
                <input type="checkbox" class="d-none" name="${select.name}[]" value="${option.value}" ${
                option.selected ? "checked" : ""
            }>
                ${option.text}
            `;
            dropdown.appendChild(item);

            if (option.selected) {
                createTag(option.value, option.text.trim());
            }
        });

        // Hide original select
        select.style.display = "none";
        select.name = "";
        select.parentNode.insertBefore(wrapper, select);

        // Toggle dropdown visibility
        display.addEventListener("click", () => {
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        });

        document.addEventListener("click", (e) => {
            if (!wrapper.contains(e.target)) {
                dropdown.style.display = "none";
            }
        });

        // Handle checkbox changes
        dropdown.addEventListener("change", (e) => {
            if (e.target.tagName === "INPUT" && e.target.type === "checkbox") {
                const value = e.target.value;
                const isChecked = e.target.checked;

                if (isChecked) {
                    createTag(value, e.target.parentNode.textContent.trim());
                    select.querySelector(`option[value="${value}"]`).selected = true;
                } else {
                    removeTag(value);
                    select.querySelector(`option[value="${value}"]`).selected = false;
                }
                updatePlaceholder();
            }
        });

        // Search functionality
        searchInput.addEventListener("input", () => {
            const query = searchInput.value.toLowerCase();
            Array.from(dropdown.querySelectorAll("label")).forEach((label) => {
                const text = label.textContent.toLowerCase();
                label.style.display = text.includes(query) ? "block" : "none";
            });
        });

        function createTag(value, text) {
            const existingTag = display.querySelector(`.tag[data-value="${value}"]`);
            if (existingTag) return;

            const tag = document.createElement("span");
            tag.classList.add("tag");
            tag.setAttribute("data-value", value);
            tag.innerHTML = `
                ${truncateText(text.trim())}
                <span class="remove-tag">&times;</span>
            `;
            tag.querySelector(".remove-tag").addEventListener("click", () => {
                removeTag(value);
                select.querySelector(`option[value="${value}"]`).selected = false;
                dropdown.querySelector(`input[value="${value}"]`).checked = false;
                updatePlaceholder();
            });
            display.appendChild(tag);
        }

        function removeTag(value) {
            const tag = display.querySelector(`.tag[data-value="${value}"]`);
            if (tag) tag.remove();
        }

        function updatePlaceholder() {
            const tags = display.querySelectorAll(".tag");
            placeholderSpan.style.display = tags.length > 0 ? "none" : "inline";
        }

        function truncateText(text, length = 10) {
            return text.length > length ? text.slice(0, length) + "..." : text;
        }
    }

    // Initialize all custom multiselects
    document.querySelectorAll("select.custom-multiselect").forEach(setupCustomMultiselect);
})();
</script>