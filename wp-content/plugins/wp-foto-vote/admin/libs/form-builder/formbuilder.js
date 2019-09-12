(function() {
  rivets.binders.input = {
    publishes: true,
    routine: rivets.binders.value.routine,
    bind: function(el) {
      return $(el).bind('input.rivets', this.publish);
    },
    unbind: function(el) {
      return $(el).unbind('input.rivets');
    }
  };

  rivets.configure({
    prefix: "rv",
    adapter: {
      subscribe: function(obj, keypath, callback) {
        callback.wrapped = function(m, v) {
          return callback(v);
        };
        return obj.on('change:' + keypath, callback.wrapped);
      },
      unsubscribe: function(obj, keypath, callback) {
        return obj.off('change:' + keypath, callback.wrapped);
      },
      read: function(obj, keypath) {
        if (keypath === "cid") {
          return obj.cid;
        }
        return obj.get(keypath);
      },
      publish: function(obj, keypath, value) {
        if (obj.cid) {
          return obj.set(keypath, value);
        } else {
          return obj[keypath] = value;
        }
      }
    }
  });

}).call(this);

(function() {
  var BuilderView, EditFieldView, Formbuilder, FormbuilderCollection, FormbuilderModel, ViewFieldView,
    extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    hasProp = {}.hasOwnProperty;

  FormbuilderModel = (function(superClass) {
    extend(FormbuilderModel, superClass);

    function FormbuilderModel() {
      return FormbuilderModel.__super__.constructor.apply(this, arguments);
    }

    FormbuilderModel.prototype.sync = function() {};

    FormbuilderModel.prototype.indexInDOM = function() {
      var $wrapper;
      $wrapper = $(".fb-field-wrapper").filter(((function(_this) {
        return function(_, el) {
          return $(el).data('cid') === _this.cid;
        };
      })(this)));
      return $(".fb-field-wrapper").index($wrapper);
    };

    FormbuilderModel.prototype.is_input = function() {
      return Formbuilder.inputFields[this.get(Formbuilder.options.mappings.FIELD_TYPE)] != null;
    };

    return FormbuilderModel;

  })(Backbone.DeepModel);

  FormbuilderCollection = (function(superClass) {
    extend(FormbuilderCollection, superClass);

    function FormbuilderCollection() {
      return FormbuilderCollection.__super__.constructor.apply(this, arguments);
    }

    FormbuilderCollection.prototype.initialize = function() {
      return this.on('add', this.copyCidToModel);
    };

    FormbuilderCollection.prototype.model = FormbuilderModel;

    FormbuilderCollection.prototype.comparator = function(model) {
      return model.indexInDOM();
    };

    FormbuilderCollection.prototype.copyCidToModel = function(model) {
      return model.attributes.cid = model.cid;
    };

    return FormbuilderCollection;

  })(Backbone.Collection);

  ViewFieldView = (function(superClass) {
    extend(ViewFieldView, superClass);

    function ViewFieldView() {
      return ViewFieldView.__super__.constructor.apply(this, arguments);
    }

    ViewFieldView.prototype.className = "fb-field-wrapper";

    ViewFieldView.prototype.events = {
      'click .subtemplate-wrapper': 'focusEditView',
      'click .js-duplicate': 'duplicate',
      'click .js-clear': 'clear'
    };

    ViewFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      this.listenTo(this.model, "change", this.render);
      return this.listenTo(this.model, "destroy", this.remove);
    };

    ViewFieldView.prototype.render = function() {
      if ((this.model.changed["field_options"] != null) && (this.model.changed.field_options["save_to"] != null)) {
        if (this.model.changed.field_options.save_to === "none") {
          this.parentView.editView.$el.find(".fb-common-save_to_key").removeClass("hidden");
        } else {
          this.parentView.editView.$el.find(".fb-common-save_to_key").addClass("hidden");
        }
      }
      this.$el.addClass('response-field-' + this.model.get(Formbuilder.options.mappings.FIELD_TYPE)).data('cid', this.model.cid).html(Formbuilder.templates["view/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      return this;
    };

    ViewFieldView.prototype.focusEditView = function() {
      this.parentView.createAndShowEditView(this.model);
      if (this.model.attributes.field_type === "file") {
        return this.parentView;
      }
      if (this.model.attributes.field_options.save_to === "none") {
        this.parentView.editView.$el.find(".fb-common-save_to_key").removeClass("hidden");
      } else {
        this.parentView.editView.$el.find(".fb-common-save_to_key").addClass("hidden");
      }
      return this.parentView;
    };

    ViewFieldView.prototype.clear = function(e) {
      var cb, x;
      e.preventDefault();
      e.stopPropagation();
      cb = (function(_this) {
        return function() {
          _this.parentView.handleFormUpdate();
          return _this.model.destroy();
        };
      })(this);
      x = Formbuilder.options.CLEAR_FIELD_CONFIRM;
      switch (typeof x) {
        case 'string':
          if (confirm(x)) {
            return cb();
          }
          break;
        case 'function':
          return x(cb);
        default:
          return cb();
      }
    };

    ViewFieldView.prototype.duplicate = function() {
      var attrs;
      attrs = _.clone(this.model.attributes);
      delete attrs['id'];
      attrs['label'] += ' Copy';
      return this.parentView.createField(attrs, {
        position: this.model.indexInDOM() + 1
      });
    };

    return ViewFieldView;

  })(Backbone.View);

  EditFieldView = (function(superClass) {
    extend(EditFieldView, superClass);

    function EditFieldView() {
      return EditFieldView.__super__.constructor.apply(this, arguments);
    }

    EditFieldView.prototype.className = "edit-response-field";

    EditFieldView.prototype.events = {
      'click .js-add-option': 'addOption',
      'click .js-remove-option': 'removeOption',
      'click .js-default-updated': 'defaultUpdated',
      'input .option-label-input': 'forceRender'
    };

    EditFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      return this.listenTo(this.model, "destroy", this.remove);
    };

    EditFieldView.prototype.render = function() {
      this.$el.html(Formbuilder.templates["edit/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      rivets.bind(this.$el, {
        model: this.model
      });
      return this;
    };

    EditFieldView.prototype.remove = function() {
      this.parentView.editView = void 0;
      this.parentView.$el.find("[data-target=\"#addField\"]").click();
      return EditFieldView.__super__.remove.apply(this, arguments);
    };

    EditFieldView.prototype.addOption = function(e) {
      var $el, i, newOption, options;
      $el = $(e.currentTarget);
      i = this.$el.find('.option').index($el.closest('.option'));
      options = this.model.get(Formbuilder.options.mappings.OPTIONS) || [];
      newOption = {
        label: "",
        checked: false
      };
      if (i > -1) {
        options.splice(i + 1, 0, newOption);
      } else {
        options.push(newOption);
      }
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.removeOption = function(e) {
      var $el, index, options;
      $el = $(e.currentTarget);
      index = this.$el.find(".js-remove-option").index($el);
      options = this.model.get(Formbuilder.options.mappings.OPTIONS);
      options.splice(index, 1);
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.defaultUpdated = function(e) {
      var $el;
      $el = $(e.currentTarget);
      if (this.model.get(Formbuilder.options.mappings.FIELD_TYPE) !== 'checkboxes') {
        this.$el.find(".js-default-updated").not($el).attr('checked', false).trigger('change');
      }
      return this.forceRender();
    };

    EditFieldView.prototype.forceRender = function() {
      return this.model.trigger('change');
    };

    return EditFieldView;

  })(Backbone.View);

  BuilderView = (function(superClass) {
    extend(BuilderView, superClass);

    function BuilderView() {
      return BuilderView.__super__.constructor.apply(this, arguments);
    }

    BuilderView.prototype.SUBVIEWS = [];

    BuilderView.prototype.events = {
      'click .js-save-form': 'saveForm',
      'click .js-restore-form': 'restoreForm',
      'click .fb-tabs a': 'showTab',
      'click .fb-add-field-types a': 'addField',
      'change .form-title': 'handleFormUpdate',
      'mouseover .fb-add-field-types': 'lockLeftWrapper',
      'mouseout .fb-add-field-types': 'unlockLeftWrapper'
    };

    BuilderView.prototype.initialize = function(options) {
      var selector;
      selector = options.selector, this.formBuilder = options.formBuilder, this.bootstrapData = options.bootstrapData, this.bootstrapTitle = options.bootstrapTitle;
      if (selector != null) {
        this.setElement($(selector));
      }
      this.collection = new FormbuilderCollection;
      this.collection.bind('add', this.addOne, this);
      this.collection.bind('reset', this.reset, this);
      this.collection.bind('change', this.handleFormUpdate, this);
      this.collection.bind('destroy add reset', this.hideShowNoResponseFields, this);
      this.collection.bind('destroy', this.ensureEditViewScrolled, this);
      this.render();
      this.collection.reset(this.bootstrapData);
      this.bindSaveEvent();
      return jQuery(".form-title").val(this.bootstrapTitle);
    };

    BuilderView.prototype.bindSaveEvent = function() {
      this.formSaved = true;
      this.saveFormButton = this.$el.find(".js-save-form");
      this.saveFormButton.attr('disabled', true).text(Formbuilder.options.dict.ALL_CHANGES_SAVED);
      if (!!Formbuilder.options.AUTOSAVE) {
        setInterval((function(_this) {
          return function() {
            return _this.saveForm.call(_this);
          };
        })(this), 5000);
      }
      return $(window).bind('beforeunload', (function(_this) {
        return function() {
          if (_this.formSaved) {
            return void 0;
          } else {
            return Formbuilder.options.dict.UNSAVED_CHANGES;
          }
        };
      })(this));
    };

    BuilderView.prototype.reset = function() {
      this.$responseFields.html('');
      return this.addAll();
    };

    BuilderView.prototype.render = function() {
      var j, len, ref, subview;
      this.$el.html(Formbuilder.templates['page']());
      this.$fbLeft = this.$el.find('.fb-left');
      this.$responseFields = this.$el.find('.fb-response-fields');
      this.bindWindowScrollEvent();
      this.hideShowNoResponseFields();
      ref = this.SUBVIEWS;
      for (j = 0, len = ref.length; j < len; j++) {
        subview = ref[j];
        new subview({
          parentView: this
        }).render();
      }
      return this;
    };

    BuilderView.prototype.bindWindowScrollEvent = function() {
      return $(window).on('scroll', (function(_this) {
        return function() {
          var maxMargin, newMargin;
          if (_this.$fbLeft.data('locked') === true) {
            return;
          }
          newMargin = Math.max(0, $(window).scrollTop() - _this.$el.offset().top);
          maxMargin = _this.$responseFields.height();
          return _this.$fbLeft.css({
            'margin-top': Math.min(maxMargin, newMargin)
          });
        };
      })(this));
    };

    BuilderView.prototype.showTab = function(e) {
      var $el, first_model, target;
      $el = $(e.currentTarget);
      target = $el.data('target');
      $el.closest('li').addClass('active').siblings('li').removeClass('active');
      $(target).addClass('active').siblings('.fb-tab-pane').removeClass('active');
      if (target !== '#editField') {
        this.unlockLeftWrapper();
      }
      if (target === '#editField' && !this.editView && (first_model = this.collection.models[0])) {
        return this.createAndShowEditView(first_model);
      }
    };

    BuilderView.prototype.addOne = function(responseField, _, options) {
      var $replacePosition, view;
      view = new ViewFieldView({
        model: responseField,
        parentView: this
      });
      if (options.$replaceEl != null) {
        return options.$replaceEl.replaceWith(view.render().el);
      } else if ((options.position == null) || options.position === -1) {
        return this.$responseFields.append(view.render().el);
      } else if (options.position === 0) {
        return this.$responseFields.prepend(view.render().el);
      } else if (($replacePosition = this.$responseFields.find(".fb-field-wrapper").eq(options.position))[0]) {
        return $replacePosition.before(view.render().el);
      } else {
        return this.$responseFields.append(view.render().el);
      }
    };

    BuilderView.prototype.setSortable = function() {
      if (this.$responseFields.hasClass('ui-sortable')) {
        this.$responseFields.sortable('destroy');
      }
      this.$responseFields.sortable({
        forcePlaceholderSize: true,
        placeholder: 'sortable-placeholder',
        stop: (function(_this) {
          return function(e, ui) {
            var rf;
            if (ui.item.data('field-type')) {
              rf = _this.collection.create(Formbuilder.helpers.defaultFieldAttrs(ui.item.data('field-type')), {
                $replaceEl: ui.item
              });
              _this.createAndShowEditView(rf);
            }
            _this.handleFormUpdate();
            return true;
          };
        })(this),
        update: (function(_this) {
          return function(e, ui) {
            if (!ui.item.data('field-type')) {
              return _this.ensureEditViewScrolled();
            }
          };
        })(this)
      });
      return this.setDraggable();
    };

    BuilderView.prototype.setDraggable = function() {
      var $addFieldButtons;
      $addFieldButtons = this.$el.find("[data-field-type]");
      return $addFieldButtons.draggable({
        connectToSortable: this.$responseFields,
        helper: (function(_this) {
          return function() {
            var $helper;
            $helper = $("<div class='response-field-draggable-helper' />");
            $helper.css({
              width: _this.$responseFields.width(),
              height: '80px'
            });
            return $helper;
          };
        })(this)
      });
    };

    BuilderView.prototype.addAll = function() {
      this.collection.each(this.addOne, this);
      return this.setSortable();
    };

    BuilderView.prototype.hideShowNoResponseFields = function() {
      return this.$el.find(".fb-no-response-fields")[this.collection.length > 0 ? 'hide' : 'show']();
    };

    BuilderView.prototype.addField = function(e) {
      var field_type;
      field_type = $(e.currentTarget).data('field-type');
      return this.createField(Formbuilder.helpers.defaultFieldAttrs(field_type));
    };

    BuilderView.prototype.createField = function(attrs, options) {
      var rf;
      rf = this.collection.create(attrs, options);
      this.createAndShowEditView(rf);
      return this.handleFormUpdate();
    };

    BuilderView.prototype.createAndShowEditView = function(model) {
      var $newEditEl, $responseFieldEl;
      $responseFieldEl = this.$el.find(".fb-field-wrapper").filter(function() {
        return $(this).data('cid') === model.cid;
      });
      $responseFieldEl.addClass('editing').siblings('.fb-field-wrapper').removeClass('editing');
      if (this.editView) {
        if (this.editView.model.cid === model.cid) {
          this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
          this.scrollLeftWrapper($responseFieldEl);
          return;
        }
        this.editView.remove();
      }
      this.editView = new EditFieldView({
        model: model,
        parentView: this
      });
      $newEditEl = this.editView.render().$el;
      this.$el.find(".fb-edit-field-wrapper").html($newEditEl);
      this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
      this.scrollLeftWrapper($responseFieldEl);
      return this;
    };

    BuilderView.prototype.ensureEditViewScrolled = function() {
      if (!this.editView) {
        return;
      }
      return this.scrollLeftWrapper($(".fb-field-wrapper.editing"));
    };

    BuilderView.prototype.scrollLeftWrapper = function($responseFieldEl) {
      this.unlockLeftWrapper();
      if (!$responseFieldEl[0]) {
        return;
      }
      return $.scrollWindowTo((this.$el.offset().top + $responseFieldEl.offset().top) - this.$responseFields.offset().top, 200, (function(_this) {
        return function() {
          return _this.lockLeftWrapper();
        };
      })(this));
    };

    BuilderView.prototype.lockLeftWrapper = function() {
      return this.$fbLeft.data('locked', true);
    };

    BuilderView.prototype.unlockLeftWrapper = function() {
      return this.$fbLeft.data('locked', false);
    };

    BuilderView.prototype.handleFormUpdate = function() {
      if (this.updatingBatch) {
        return;
      }
      this.formSaved = false;
      return this.saveFormButton.removeAttr('disabled').text(Formbuilder.options.dict.SAVE_FORM);
    };

    BuilderView.prototype.saveForm = function(e) {
      var payload;
      if (this.formSaved) {
        return;
      }
      this.formSaved = true;
      this.saveFormButton.attr('disabled', true).text(Formbuilder.options.dict.ALL_CHANGES_SAVED);
      this.collection.sort();
      payload = {
        fields: JSON.stringify(this.collection.toJSON()),
        title: jQuery(".form-title").val()
      };
      if (Formbuilder.options.HTTP_ENDPOINT) {
        this.doAjaxSave(payload);
      }
      return this.formBuilder.trigger('save', payload);
    };

    BuilderView.prototype.doAjaxSave = function(payload) {
      return $.ajax({
        url: fv_add_query_arg('action', Formbuilder.options.HTTP_SAVE_ACTION, Formbuilder.options.HTTP_ENDPOINT),
        type: Formbuilder.options.HTTP_METHOD,
        data: payload,
        success: (function(_this) {
          return function(data) {
            data = FvLib.parseJson(data);
            if (data.success) {
              return jQuery.growl.notice({
                message: 'Form Saved!'
              });
            } else {
              return jQuery.growl.warning({
                message: data.message
              });
            }
          };
        })(this)
      });
    };

    BuilderView.prototype.restoreForm = function() {
      return $.ajax({
        url: fv_add_query_arg('action', Formbuilder.options.HTTP_RESET_ACTION, Formbuilder.options.HTTP_ENDPOINT),
        processData: false,
        contentType: false,
        success: (function(_this) {
          return function(data) {
            var fb;
            data = FvLib.parseJson(data);
            if (data.success && data.fields) {
              jQuery('.fb-main').html("");
              fb = new Formbuilder({
                selector: '.fb-main',
                bootstrapData: jQuery.parseJSON(data.fields, {
                  bootstrapTitle: _this.bootstrapTitle
                })
              });
              return jQuery.growl.notice({
                message: 'Default form restored!'
              });
            } else {
              return jQuery.growl.warning({
                message: data.message
              });
            }
          };
        })(this)
      });
    };

    return BuilderView;

  })(Backbone.View);

  Formbuilder = (function() {
    Formbuilder.helpers = {
      defaultFieldAttrs: function(field_type) {
        var attrs, base;
        attrs = {};
        attrs[Formbuilder.options.mappings.LABEL] = 'Untitled';
        attrs['field_options'] = {};
        attrs[Formbuilder.options.mappings.SAVE_FORMAT] = '{value}';
        attrs[Formbuilder.options.mappings.SAVE_KEY] = field_type + '_' + (Math.floor(Math.random() * (99999 - 999 + 1)) + 999);
        attrs[Formbuilder.options.mappings.SAVE_TO] = "none";
        attrs[Formbuilder.options.mappings.FIELD_TYPE] = field_type;
        attrs[Formbuilder.options.mappings.REQUIRED] = true;
        attrs[Formbuilder.options.mappings.WIDTH] = '1-1';
        return (typeof (base = Formbuilder.fields[field_type]).defaultAttributes === "function" ? base.defaultAttributes(attrs) : void 0) || attrs;
      },
      simple_format: function(x) {
        return x != null ? x.replace(/\n/g, '<br />') : void 0;
      }
    };

    Formbuilder.options = {
      BUTTON_CLASS: 'fb-button',
      HTTP_ENDPOINT: '',
      HTTP_METHOD: 'POST',
      HTTP_SAVE_ACTION: '',
      HTTP_RESET_ACTION: '',
      FORM_ID: '',
      AUTOSAVE: false,
      CLEAR_FIELD_CONFIRM: false,
      mappings: {
        SHOW_TO: 'field_options.show_to',
        SAVE_KEY: 'field_options.save_key',
        SAVE_TO: 'field_options.save_to',
        SAVE_FORMAT: 'field_options.save_format',
        DEFAULT_VALUE: 'field_options.default_value',
        PLACEHOLDER: 'placeholder',
        SIZE: 'field_options.size',
        UNITS: 'field_options.units',
        LABEL: 'label',
        FIELD_TYPE: 'field_type',
        MULTI_UPLOAD: 'field_options.multi_upload',
        MULTI_COUNT: 'field_options.multi_count',
        MULTI_SHOW_PHOTO_NAME: 'field_options.multi_show_photo_name',
        REQUIRED: 'required',
        ADMIN_ONLY: 'admin_only',
        OPTIONS: 'field_options.options',
        DESCRIPTION: 'field_options.description',
        INCLUDE_OTHER: 'field_options.include_other_option',
        INCLUDE_BLANK: 'field_options.include_blank_option',
        MIN: 'field_options.min',
        MAX: 'field_options.max',
        MINLENGTH: 'field_options.minlength',
        MAXLENGTH: 'field_options.maxlength',
        ICON: 'field_options.icon',
        DATE_FORMAT: 'field_options.date_format',
        DATE_DD: 'field_options.date_day_label',
        DATE_MM: 'field_options.date_month_label',
        DATE_YY: 'field_options.date_year_label',
        CHECKED: 'field_options.checked',
        WIDTH: 'width',
        FORMAT: 'field_options.format'
      },
      dict: {
        ALL_CHANGES_SAVED: 'All changes saved',
        RESTORE_DEFAULT: 'Restore default form',
        SAVE_FORM: 'Save form',
        UNSAVED_CHANGES: 'You have unsaved changes. If you leave this page, you will lose those changes!'
      }
    };

    Formbuilder.fields = {};

    Formbuilder.inputFields = {};

    Formbuilder.nonInputFields = {};

    Formbuilder.registerField = function(name, opts) {
      var j, len, ref, x;
      ref = ['view', 'edit'];
      for (j = 0, len = ref.length; j < len; j++) {
        x = ref[j];
        opts[x] = _.template(opts[x]);
      }
      opts.field_type = name;
      Formbuilder.fields[name] = opts;
      if (opts.type === 'non_input') {
        return Formbuilder.nonInputFields[name] = opts;
      } else {
        return Formbuilder.inputFields[name] = opts;
      }
    };

    function Formbuilder(opts) {
      var args;
      if (opts == null) {
        opts = {};
      }
      _.extend(this, Backbone.Events);
      args = _.extend(opts, {
        formBuilder: this
      });
      this.mainView = new BuilderView(args);
    }

    return Formbuilder;

  })();

  window.Formbuilder = Formbuilder;

  if (typeof module !== "undefined" && module !== null) {
    module.exports = Formbuilder;
  } else {
    window.Formbuilder = Formbuilder;
  }

}).call(this);

(function() {
  Formbuilder.registerField('category', {
    order: 25,
    view: "<select>\n\n  <option value=''><%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %></option>\n\n</select>",
    edit: "  ",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-arrow-sorted-down\"></span></span> Category",
    defaultAttributes: function(attrs) {
      attrs.placeholder = "== Select category ==";
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('rules_checkbox', {
    order: 11,
    view: "<div>\n  <label class='fb-option'>\n    <input type='checkbox' <%= rf.get(Formbuilder.options.mappings.CHECKED) && 'checked' %> onclick=\"javascript: return false;\" />\n    <%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>\n  </label>\n</div>",
    edit: "<div class='fb-edit-section-header'>Default state</div>\n<label>\n  <input type='checkbox' data-rv-checked='model.<%= Formbuilder.options.mappings.CHECKED %>' />\n  Checked by default?\n</label>\n\n<div>* <small>Note: this field state will be not saved in competitor details</small></div>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-input-checked\"></span></span> Rules checkbox",
    defaultAttributes: function(attrs) {
      attrs[Formbuilder.options.mappings.CHECKED] = false;
      attrs[Formbuilder.options.mappings.PLACEHOLDER] = "Please check me";
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('checkboxes', {
    order: 10,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='checkbox' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if ( false && rf.get(Formbuilder.options.mappings.INCLUDE_OTHER) ) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='checkbox' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther2: false }) %>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-media-stop-outline\"></span></span> Checkboxes",
    defaultAttributes: function(attrs) {
      attrs[Formbuilder.options.mappings.REQUIRED] = false;
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('date', {
    order: 20,
    view: "<div class='input-line'>\n  <span class='month'>\n    <input type=\"text\" />\n    <label><%= rf.get(Formbuilder.options.mappings.DATE_DD) %></label>\n  </span>\n\n  <span class='above-line'>/</span>\n\n  <span class='day'>\n    <input type=\"text\" />\n    <label><%= rf.get(Formbuilder.options.mappings.DATE_MM) %></label>\n  </span>\n\n  <span class='above-line'>/</span>\n\n  <span class='year'>\n    <input type=\"text\" />\n    <label><%= rf.get(Formbuilder.options.mappings.DATE_YY) %></label>\n  </span>\n</div>",
    edit: "<div class=\"fb-edit-section-header\">Date format</div>\n<select data-rv-value='model.<%= Formbuilder.options.mappings.DATE_FORMAT %>'>\n    <option value=\"dd.mm.yyyy.\">dd.mm.yyyy</option>\n    <option value=\"dd-mm-yyyy-\">dd-mm-yyyy</option>\n    <option value=\"dd/mm/yyyy/\">dd/mm/yyyy</option>\n    <option value=\"mm/dd/yyyy/\">mm/dd/yyyy (USA)</option>\n    <option value=\"yyyy-mm-dd-\">yyyy-mm-dd (ISO 8601)</option>\n</select>  \n<div class='fb-edit-section-header'>Day label:</div>\n<input type='input' data-rv-input='model.<%= Formbuilder.options.mappings.DATE_DD %>' value='DD'/>    \n<div class='fb-edit-section-header'>Month label:</div>\n<input type='input' data-rv-input='model.<%= Formbuilder.options.mappings.DATE_MM %>' value='MM'/>    <div class='fb-edit-section-header'>Year label:</div>\n<input type='input' data-rv-input='model.<%= Formbuilder.options.mappings.DATE_YY %>' value='YY'/>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-calendar\"></span></span> Date",
    defaultAttributes: function(attrs) {
      attrs.field_options.date_format = 'dd.mm.yyyy.';
      attrs.field_options.date_day_label = 'DD';
      attrs.field_options.date_month_label = 'MM';
      attrs.field_options.date_year_label = 'YY';
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('select', {
    order: 24,
    view: "<select>\n  <% if (rf.get(Formbuilder.options.mappings.INCLUDE_BLANK)) { %>\n    <option value=''></option>\n  <% } %>\n\n  <% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n    <option <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'selected' %>>\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </option>\n  <% } %>\n</select>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeBlank: false }) %>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-arrow-sorted-down\"></span></span> Dropdown",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      attrs.field_options.include_blank_option = false;
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('email', {
    order: 40,
    view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' />",
    edit: "<%= Formbuilder.templates['edit/max/default_value']() %>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-mail\"></span></span> Email",
    defaultAttributes: function(attrs) {
      attrs.field_options.save_to = "user_email";
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('file', {
    icon: '<span class="typcn typcn-document-add"></span>',
    order: 0,
    view: "",
    edit: "<div class='fb-edit-section-header'>Label</div>\n\n    <div class='fb-common-wrapper'>\n        <div class='fb-label-description'>\n            <input type='text' data-rv-input='model.<%= Formbuilder.options.mappings.LABEL %>'/>\n            <textarea data-rv-input='model.<%= Formbuilder.options.mappings.DESCRIPTION %>'\n  placeholder='Short description under file input, like `max size - 1mb`'></textarea>\n        </div> \n        <div class='fb-clear'></div> \n    </div>\n<div class='fb-edit-section-header'>Enable multi upload photos?</div>\n<input type='checkbox' data-rv-checked='model.<%= Formbuilder.options.mappings.MULTI_UPLOAD %>' value='on'/> Yes <small>(allow user upload more than 1 photo in one from submit)</small>\n<div class='fb-edit-section-header'>Show `photo name` field near each file input?</div>\n<input type='checkbox' data-rv-checked='model.<%= Formbuilder.options.mappings.MULTI_SHOW_PHOTO_NAME %>' value='on'/> Yes <small>(if you selected this option, than you need remove from form fields `photo name`, because it will be overridden by this option)</small>\n<div class='fb-edit-section-header'>Input `photo name` placeholder:</div>\n<input type='input' data-rv-input='model.<%= Formbuilder.options.mappings.PLACEHOLDER %>' value='Enter photo name'/>\n<div class='fb-edit-section-header'>Max files count, if enabled multi upload?</div>\n<input type='number' min='2' max='10' data-rv-input='model.<%= Formbuilder.options.mappings.MULTI_COUNT %>' value='on'/> 2-10 <small>(better if this number will be equal to `The maximum number of one user upload photos` from Contest settings)</small>",
    type: 'non_input',
    addButton: "<span class='symbol'><span class='typcn typcn-document-add'></span></span> File"
  });

}).call(this);

(function() {
  Formbuilder.registerField('number', {
    order: 30,
    view: "<input type='text' />\n<% if (units = rf.get(Formbuilder.options.mappings.UNITS)) { %>\n  <%= units %>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/min_max']() %>\n<%= Formbuilder.templates['edit/units']() %>\n<%= Formbuilder.templates['edit/integer_only']() %>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-sort-numerically\"></span></span> Number"
  });

}).call(this);

(function() {
  Formbuilder.registerField('textarea', {
    order: 5,
    view: "<textarea class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>'></textarea>",
    edit: "<%= Formbuilder.templates['edit/max/default_value']() %>\n<%= Formbuilder.templates['edit/size']() %>\n<%= Formbuilder.templates['edit/min_max_length']() %>",
    addButton: "<span class=\"symbol\">&#182;</span> Paragraph",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('phone', {
    order: 19,
    view: "<div>\n    <input type='tel' placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' />\n</div>",
    edit: "<div class='fb-edit-section-header'>Mask format</div>\n<input type='text' data-rv-input='model.<%= Formbuilder.options.mappings.FORMAT %>' />\n\n<div>\n    <small><strong>Note:</strong>\n    <br/>a - Represents an alpha character (A-Z,a-z)\n    <br/>9 - Represents a numeric character (0-9)\n    <br/>* - Represents an alphanumeric character (A-Z,a-z,0-9)</small>\n    <br/><small>Example: +38(099) 999 99 99, where \"9\" must be integers</small>\n</div>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-phone\"></span></span> Phone",
    defaultAttributes: function(attrs) {
      attrs[Formbuilder.options.mappings.LABEL] = 'Phone';
      attrs[Formbuilder.options.mappings.FORMAT] = '+1-999-999-9999';
      attrs[Formbuilder.options.mappings.PLACEHOLDER] = "+1-___-___-____";
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('radio', {
    order: 15,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='radio' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (  false && rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='radio' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther2: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-media-record-outline\"></span></span> Multiple Choice",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('section_break', {
    order: 0,
    type: 'non_input',
    view: "<%= Formbuilder.templates['view/duplicate_remove']() %>",
    edit: "<div class='fb-edit-section-header'>Label</div>\n<input type='text' data-rv-input='model.<%= Formbuilder.options.mappings.LABEL %>' />\n<textarea data-rv-input='model.<%= Formbuilder.options.mappings.DESCRIPTION %>'\n  placeholder='Add a longer description to this field'></textarea>",
    addButton: "<span class='symbol'><span class='typcn typcn-minus'></span></span> Section Break",
    defaultAttributes: function(attrs) {
      attrs.required = false;
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('text', {
    order: 0,
    view: "<input type='text' class='' placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' />",
    edit: "<%= Formbuilder.templates['edit/max/default_value']() %>    \n<%= Formbuilder.templates['edit/min_max_length']() %>",
    addButton: "<span class='symbol'><span class='typcn typcn-sort-alphabetically-outline'></span></span> Text",
    defaultAttributes: function(attrs) {
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('website', {
    order: 35,
    view: "<input type='text' placeholder='http://' />",
    edit: "<%= Formbuilder.templates['edit/max/default_value']() %>  ",
    addButton: "<span class=\"symbol\"><span class=\"typcn typcn-link\"></span></span> Website"
  });

}).call(this);

this["Formbuilder"] = this["Formbuilder"] || {};
this["Formbuilder"]["templates"] = this["Formbuilder"]["templates"] || {};

this["Formbuilder"]["templates"]["edit/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['edit/common']({field_type: rf.get(Formbuilder.options.mappings.FIELD_TYPE) }) )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['edit/max/show_to']() )) == null ? '' : __t) +
'\n';
 if ( rf.get(Formbuilder.options.mappings.FIELD_TYPE) != 'rules_checkbox' && rf.get(Formbuilder.options.mappings.FIELD_TYPE) != 'category' ) { ;
__p += '\n    ' +
((__t = ( Formbuilder.templates['edit/max/cols']() )) == null ? '' : __t) +
'\n    ' +
((__t = ( Formbuilder.templates['edit/max/save_to']() )) == null ? '' : __t) +
'\n';
 } ;
__p += '\n';
 if ( rf.get(Formbuilder.options.mappings.FIELD_TYPE) != 'date' ) { ;
__p += '\n    ' +
((__t = ( Formbuilder.templates['edit/max/placeholder']() )) == null ? '' : __t) +
'\n';
 } ;
__p += '\n\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_header"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-field-label\'>\n  <span data-rv-text="model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'"></span>\n  <code class=\'field-type\' data-rv-text=\'model.' +
((__t = ( Formbuilder.options.mappings.FIELD_TYPE )) == null ? '' : __t) +
'\'></code>\n  <span class=\'fa fa-arrow-right pull-right\'></span>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/checkboxes"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.REQUIRED )) == null ? '' : __t) +
'\' />\n  Required\n</label>\n<!-- label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.ADMIN_ONLY )) == null ? '' : __t) +
'\' />\n  Admin only\n</label -->';

}
return __p
};

this["Formbuilder"]["templates"]["edit/common"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Label</div>\n\n<div class=\'fb-common-wrapper\'>\n    <div class=\'fb-label-description\'>\n        ' +
((__t = ( Formbuilder.templates['edit/label_description']() )) == null ? '' : __t) +
'\n    </div>\n\n    ';
 if ( field_type != 'checkboxes' ) { ;
__p += '  \n        <div class=\'fb-common-checkboxes\'>\n            ' +
((__t = ( Formbuilder.templates['edit/checkboxes']() )) == null ? '' : __t) +
'\n        </div>\n    ';
 } ;
__p += '\n  <div class=\'fb-clear\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/integer_only"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Integer only</div>\n<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INTEGER_ONLY )) == null ? '' : __t) +
'\' />\n  Only accept integers\n</label>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/label_description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<input type=\'text\' data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'\' />\n<textarea data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.DESCRIPTION )) == null ? '' : __t) +
'\'\n  placeholder=\'Add a longer description to this field\'></textarea>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/max/cols"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="fb-edit-section-header">Width</div>\n<select data-rv-value=\'model.' +
((__t = ( Formbuilder.options.mappings.WIDTH )) == null ? '' : __t) +
'\'>\n    <option value="">1/1 - full width</option>\n    <option value="3-4">3/4 - 75%</option>\n    <option value="2-3">2/3 - 66,6%</option>\n    <option value="1-2">1/2 - 50%</option>\n    <option value="1-3">1/3 - 33,3%</option>\n    <option value="1-4">1/4 - 25%</option>\n</select>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/max/default_value"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="fb-edit-section-header">Default value (if logged in)</div>\n<select data-rv-value=\'model.' +
((__t = ( Formbuilder.options.mappings.DEFAULT_VALUE )) == null ? '' : __t) +
'\'>\n      <option value="">Empty</option>\n      <option value="email">User email</option>\n      <option value="display_name">User display name</option>\n      <option value="first_name">User first name</option>\n      <option value="last_name">User last name</option>\n</select>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/max/placeholder"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="fb-edit-section-header">Placeholder</div>\n<input type="text" data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.PLACEHOLDER )) == null ? '' : __t) +
'\'/>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/max/save_to"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="fb-edit-section-header">Save to photo field</div>\n    <select data-rv-value=\'model.' +
((__t = ( Formbuilder.options.mappings.SAVE_TO )) == null ? '' : __t) +
'\'>\n        <option value="none">Default (meta)</option>\n        <option value="name">Name (max. 255 chars)</option>\n        <option value="description">Description (max. 500 chars)</option>\n        <option value="full_description">Full description (max. 1255 chars)</option>\n        <option value="user_email">User email</option>\n    </select>\n\n<div class="fb-edit-section-header">\n    Save format \n    <small>(Tag {value} is required! If it"s empty, there will be used default format — "{value}"; Example: "{value} years old")</small>\n</div>\n<input type="text" data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.SAVE_FORMAT )) == null ? '' : __t) +
'\' />\n\n<div class="fb-common-save_to_key">\n    <div class="fb-edit-section-header">Meta key\n        <small>(This key can be used for output this field to public. Example: {meta_company_name})</small>\n    </div>\n    <input type="text" data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.SAVE_KEY )) == null ? '' : __t) +
'\'/><small>ex.: company_name</small>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/max/show_to"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="fb-edit-section-header">Show field to</div>\r\n    <select data-rv-value=\'model.' +
((__t = ( Formbuilder.options.mappings.SHOW_TO )) == null ? '' : __t) +
'\'>\r\n        <option value="">All users</option>\r\n        <option value="no_logged">Not logged users</option>\r\n        <option value="logged">Logged users</option>\r\n    </select>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/min_max"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Minimum / Maximum</div>\n\nAbove\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MIN )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\nBelow\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAX )) == null ? '' : __t) +
'" style="width: 30px" />\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/min_max_length"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Length Limit</div>\n\nMin\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MINLENGTH )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\nMax\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAXLENGTH )) == null ? '' : __t) +
'" style="width: 30px" />\n\ncharacters';

}
return __p
};

this["Formbuilder"]["templates"]["edit/options"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Options</div>\n\n';
 if (typeof includeBlank !== 'undefined' && includeBlank !== false){ ;
__p += '\n  <label>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_BLANK )) == null ? '' : __t) +
'\' />\n    Include blank\n  </label>\n';
 } ;
__p += '\n\n<div class=\'option\' data-rv-each-option=\'model.' +
((__t = ( Formbuilder.options.mappings.OPTIONS )) == null ? '' : __t) +
'\'>\n  <input type="checkbox" class=\'js-default-updated\' data-rv-checked="option:checked" />\n  <input type="text" data-rv-input="option:label" class=\'option-label-input\' />\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Add Option"><i class=\'typcn typcn-plus\'></i></a>\n  <a class="js-remove-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Option"><i class=\'typcn typcn-minus\'></i></a>\n</div>\n\n';
 if (typeof includeOther !== 'undefined'){ ;
__p += '\n  <label>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_OTHER )) == null ? '' : __t) +
'\' />\n    Include "other"\n  </label>\n';
 } ;
__p += '\n\n<div class=\'fb-bottom-add\'>\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">Add option</a>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/size"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Size</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.SIZE )) == null ? '' : __t) +
'">\n  <option value="small">Small</option>\n  <option value="medium">Medium</option>\n  <option value="large">Large</option>\n</select>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/units"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Units</div>\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.UNITS )) == null ? '' : __t) +
'" />\n';

}
return __p
};

this["Formbuilder"]["templates"]["page"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['partials/save_button']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['partials/left_side']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['partials/right_side']() )) == null ? '' : __t) +
'\n<div class=\'fb-clear\'></div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/add_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-tab-pane active\' id=\'addField\'>\n  <div class=\'fb-add-field-types\'>\n    <div class=\'section\'>\n      ';
 _.each(_.sortBy(Formbuilder.inputFields, 'order'), function(f){ ;
__p += '\n        <a data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
__p += '\n    </div>\n\n    <div class=\'section\'>\n      ';
 _.each(_.sortBy(Formbuilder.nonInputFields, 'order'), function(f){ ;
__p += '\n        <a data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
__p += '\n    </div>\n  </div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/edit_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-tab-pane\' id=\'editField\'>\n  <div class=\'fb-edit-field-wrapper\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/left_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-left\'>\n  <div class=\'form-title-wrap\'>\n    <strong>Form title:</strong> <input type="text" class="form-title">\n  </div>\n  \n  <ul class=\'fb-tabs\'>\n    <li class=\'active\'><a data-target=\'#addField\'>Add new field</a></li>\n    <li><a data-target=\'#editField\'>Edit field</a></li>\n  </ul>\n\n  <div class=\'fb-tab-content\'>\n    ' +
((__t = ( Formbuilder.templates['partials/add_field']() )) == null ? '' : __t) +
'\n    ' +
((__t = ( Formbuilder.templates['partials/edit_field']() )) == null ? '' : __t) +
'\n  </div>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/right_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-right\'>\n  <div class=\'fb-no-response-fields\'>No response fields</div>\n  <div class=\'fb-response-fields\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/save_button"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-save-wrapper\'>\n  <button class=\'js-save-form ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'\'> \n    ' +
((__t = ( Formbuilder.options.dict.SAVE_FORM )) == null ? '' : __t) +
'\n  </button>\n  <button class="js-restore-form ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n        ' +
((__t = ( Formbuilder.options.dict.RESTORE_DEFAULT )) == null ? '' : __t) +
'\n   </button>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'subtemplate-wrapper\'>\n  <div class=\'cover\'></div>\n  ' +
((__t = ( Formbuilder.templates['view/label']({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].view({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.templates['view/description']({rf: rf}) )) == null ? '' : __t) +
'\n  ' +
((__t = ( Formbuilder.templates['view/duplicate_remove']({rf: rf}) )) == null ? '' : __t) +
'\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'subtemplate-wrapper\'>\n  <div class=\'cover\'></div>\n    <label class=\'section-name\'>\n        ' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].icon )) == null ? '' : __t) +
'\n        ' +
((__t = ( rf.get(Formbuilder.options.mappings.LABEL) )) == null ? '' : __t) +
'\n        ';
 if (rf.get(Formbuilder.options.mappings.REQUIRED)) { ;
__p += '\n            <abbr title=\'required\'>*</abbr>\n        ';
 } ;
__p += '  \n    </label>\n    <p>' +
((__t = ( rf.get(Formbuilder.options.mappings.DESCRIPTION) )) == null ? '' : __t) +
'</p>\n\n    ' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].view({rf: rf}) )) == null ? '' : __t) +
'\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<span class=\'help-block\'>\n  ' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.DESCRIPTION)) )) == null ? '' : __t) +
'\n</span>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/duplicate_remove"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'actions-wrapper\'>\n  <a class="js-duplicate ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Duplicate Field"><i class=\'typcn typcn-plus\'></i></a>\n  <a class="js-clear ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Field"><i class=\'typcn typcn-minus\'></i></a>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/label"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<label>\n  <span>' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.LABEL)) )) == null ? '' : __t) +
'\n  ';
 if (rf.get(Formbuilder.options.mappings.REQUIRED)) { ;
__p += '\n    <abbr title=\'required\'>*</abbr>\n  ';
 } ;
__p += '  \n  \n  ';
 if (rf.get(Formbuilder.options.mappings.SHOW_TO)) { ;
__p += '\n    <small><i>for ' +
((__t = ( rf.get(Formbuilder.options.mappings.SHOW_TO) )) == null ? '' : __t) +
'</i></small>\n  ';
 } ;
__p += '\n</label>\n';

}
return __p
};