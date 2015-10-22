/* global window, document */

const $ = window.jQuery === undefined ? {} : window.jQuery;

class AdminMenuTabs {

  /**
   * Initalize the Admin Menu Tabs class.
   */
  static init() {
    const adminMenuTabs = new AdminMenuTabs();
    adminMenuTabs.binds();
    adminMenuTabs.setActiveTab();
  }

  /**
   * Bind elements with functions.
   */
  binds() {
    $(document).on('click', '.admin-menu-tab', this.changeTab.bind(this));
    $(document).on('wp-collapse-menu', this.setActiveTab.bind(this));
  }

  /**
   * Change tab on click.
   *
   * @param {object} $this
   */
  changeTab(e) {
    const $this      = $(e.currentTarget);
    const $adminmenu = $('#adminmenu');
    const $separator = this.getSeparator($adminmenu.find('.wp-menu-separator'));
    const tab        = $this.hasClass('admin-menu-tab-edit') ? 'edit' : 'admin';

    e.preventDefault();

    this.hideMenuItems($separator, tab);

    $.post(window.ajaxurl, {
      action: 'change_admin_menu_tab',
      tab: tab
    });
  }

  /**
   * Hide menu items.
   *
   * @param {object} $separator
   * @param {string} tab
   */
  hideMenuItems($separator, tab) {
    const $collapseMenu = $separator.parent().find('#collapse-menu');
    const $nextAll      = $separator.nextAll();
    const $prevAll      = $separator.prevAll();

    if (tab === 'edit') {
      $prevAll.show();
      $nextAll.hide();
      $collapseMenu.show().insertAfter($prevAll.first());
      $('.admin-menu-tab-admin').removeClass('active');
      $('.admin-menu-tab-edit').addClass('active');
    } else {
      $prevAll.hide();
      $nextAll.show();
      $collapseMenu.insertAfter($nextAll.eq($nextAll.length - 2));
      $('.admin-menu-tab-admin').addClass('active');
      $('.admin-menu-tab-edit').removeClass('active');
    }

    $separator.hide();
  }

  /**
   * Get separator.
   *
   * @param  {object} $separators
   *
   * @return {object}
   */
  getSeparator($separators) {
    let index = $('#adminmenutabs').data('separator');
    return $separators.eq(index);
  }

  /**
   * Set active tab.
   */
  setActiveTab() {
    const $activeTab = $('.admin-menu-tab.active');
    const $separator = this.getSeparator($('#adminmenu .wp-menu-separator'));
    const url        = window.adminMenuTabs.url;
    const location   = window.location.href.replace(url, '');
    const tab        = $activeTab.hasClass('admin-menu-tab-edit') ? 'edit' : 'admin';

    if (location !== '' && $separator.nextAll().find('a[href="' + location + '"]').length) {
      if (location === 'update-core.php') {
        const $menudashboard = $('#menu-dashboard');
        $menudashboard.removeClass('wp-has-current-submenu wp-menu-open').addClass('wp-not-current-submenu');
        $menudashboard.find('a').removeClass('wp-has-current-submenu wp-menu-open');
      }
    }

    this.hideMenuItems($separator, tab);
  }

}

AdminMenuTabs.init();
