// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later
module.exports = {
  extends: ["@nextcloud"],
  rules: {
    "import/extensions": ["error", "always", {
      "js": "never",
      "mjs": "never",
      "jsx": "never",
      "ts": "never",
      "tsx": "never",
      "vue": "never"
    }],
    "n/no-missing-import": "off",
    'vue/singleline-html-element-content-newline': 'off',
    'vue/multiline-html-element-content-newline': 'off',
    "vue/html-closing-bracket-newline": [
      "error",
      {
        "singleline": "never",
        "multiline": "always",
        "selfClosingTag": {
          "singleline": "never",
          "multiline": "always"
        }
      }
    ],
    "vue/html-self-closing": ["error", {
      "html": {
        "void": "always",
        "normal": "always",
        "component": "always"
      },
      "svg": "always",
      "math": "always"
    }],
    "vue/first-attribute-linebreak": ["error", {
      "singleline": "ignore",
      "multiline": "below"
    }],
  },
};
