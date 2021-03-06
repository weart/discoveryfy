module.exports = {
  root: true,

  parserOptions: {
    parser: 'babel-eslint',
    sourceType: 'module'
  },

  env: {
    browser: true
  },

  // https://github.com/vuejs/eslint-plugin-vue#priority-a-essential-error-prevention
  // consider switching to `plugin:vue/strongly-recommended` or `plugin:vue/recommended` for stricter rules.
  extends: [
    // 'plugin:vue/essential',
    'plugin:vue/strongly-recommended',
    // 'airbnb-base'
    // From https://github.com/api-platform/client-generator/blob/v0.3.1/.babelrc
    // 'plugin:prettier/recommended',
    // 'eslint:recommended',
  ],

  // required to lint *.vue files
  plugins: [
    'import',
    'vue',
  ],

  globals: {
    'ga': false, // Google Analytics
    'cordova': false,
    '__statics': true,
    'process': true
  },

  // add your custom rules here
  rules: {
    /*
    'no-param-reassign': 'off',

    'import/first': 'off',
    'import/named': 'error',
    'import/namespace': 'error',
    'import/default': 'error',
    'import/export': 'error',
    'import/extensions': 'off',
    'import/no-unresolved': 'off',
    'import/no-extraneous-dependencies': 'off',
    'import/prefer-default-export': 'off',
    'prefer-promise-reject-errors': 'off',
    */

    // allow console.log during development only
    'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    // allow debugger during development only
    'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off'
  }
}
