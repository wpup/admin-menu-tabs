/* global $, ajaxurl */

let $ = window.jQuery;

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
    const $this          = $(e.currentTarget);
    const $adminmenu     = $('#adminmenu');
    const $lastSeparator = $adminmenu.find('.wp-menu-separator:last');
    const tab            = $this.hasClass('admin-menu-tab-edit') ? 'edit' : 'admin';

    e.preventDefault();

    this.hideMenuItems($lastSeparator, tab);

    $.post(window.ajaxurl, {
      action: 'change_admin_menu_tab',
      tab: tab
    });
  }

  /**
   * Hide menu items.
   *
   * @param {object} $lastSeparator
   * @param {string} tab
   */
  hideMenuItems($lastSeparator, tab) {
    const $collapseMenu = $lastSeparator.parent().find('#collapse-menu');
    const $nextAll      = $lastSeparator.nextAll();
    const $prevAll      = $lastSeparator.prevAll();

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

    $lastSeparator.hide();
  }

  /**
   * Set active tab.
   */
  setActiveTab() {
    const $activeTab     = $('.admin-menu-tab.active');
    const $lastSeparator = $('#adminmenu .wp-menu-separator:last');
    const url            = window.adminMenuTabs.url;
    const location       = window.location.href.replace(url, '');
    const tab            = $activeTab.hasClass('admin-menu-tab-edit') ? 'edit' : 'admin';

    if (location !== '' && $lastSeparator.nextAll().find('a[href="' + location + '"]').length) {
      if (location === 'update-core.php') {
        const $menudashboard = $('#menu-dashboard');
        $menudashboard.removeClass('wp-has-current-submenu wp-menu-open').addClass('wp-not-current-submenu');
				$menudashboard.find('a').removeClass('wp-has-current-submenu wp-menu-open');
			}
    }

    this.hideMenuItems($lastSeparator, tab);
  }

}

AdminMenuTabs.init();
