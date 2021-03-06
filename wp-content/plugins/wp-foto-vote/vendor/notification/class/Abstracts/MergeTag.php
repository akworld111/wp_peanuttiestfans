<?php
/**
 * MergeTag abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;

/**
 * MergeTag abstract class
 */
abstract class MergeTag extends Common implements Interfaces\Taggable {

	/**
	 * MergeTag resolved value
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * MergeTag value type
	 *
	 * @var string
	 */
	protected $value_type;

	/**
	 * Short description
	 * No html tags allowed. Keep it tweet-short.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Function which resolve the merge tag value
	 *
	 * @var callable
	 */
	protected $resolver;

	/**
	 * Resolving status
	 *
	 * @var boolean
	 */
	protected $resolved = false;

	/**
	 * Trigger object, the Merge tag is assigned to
	 *
	 * @var object
	 */
	protected $trigger;

	/**
	 * If description is an example
	 *
	 * @var boolean
	 */
	protected $description_example = false;

	/**
	 * If merge tag is hidden
	 *
	 * @var boolean
	 */
	protected $hidden = false;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = array() ) {

		if ( ! isset( $params['slug'], $params['name'], $params['resolver'] ) ) {
			trigger_error( 'Merge tag requires slug, name and resolver', E_USER_ERROR );
		}

		$this->slug = $params['slug'];
		$this->name = $params['name'];

		$this->set_resolver( $params['resolver'] );

		if ( isset( $params['description'] ) ) {
			$this->description_example = isset( $params['example'] ) && $params['example'];
			$this->description         = sanitize_text_field( $params['description'] );
		}

		if ( isset( $params['hidden'] ) ) {
			$this->hidden = (bool) $params['hidden'];
		}

	}

	/**
	 * Checks if the value is the correct type
	 *
	 * @param  mixed $value tag value.
	 * @return boolean
	 */
	abstract public function validate( $value );

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param  mixed $value tag value.
	 * @return mixed        sanitized value
	 */
	abstract public function sanitize( $value );

	/**
	 * Checks the merge tag reqirements
	 * ie. if there's a property set
	 *
	 * @return boolean default always true
	 */
	public function check_requirements() {
		return true;
	}

	/**
	 * Gets description
	 *
	 * @return string description
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Resolves the merge tag value
	 * It also check if the value is correct type
	 * and sanitizes it
	 *
	 * @return mixed the resolved value
	 */
	public function resolve() {

		if ( $this->is_resolved() ) {
			return $this->get_value();
		}

		$value = call_user_func( $this->resolver, $this->get_trigger() );

		if ( ! empty( $value ) && ! $this->validate( $value ) ) {
			$error_type = E_USER_NOTICE;
			//$error_type = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? E_USER_ERROR : E_USER_NOTICE;
			trigger_error( 'Resolved value is a wrong type', $error_type );
		}

		$this->resolved = true;

		$this->value = apply_filters( 'notification/merge_tag/value/resolve', $this->sanitize( $value ) );

		return $this->get_value();

	}

	/**
	 * Checks if merge tag is already resolved
	 *
	 * @return boolean
	 */
	public function is_resolved() {
		return $this->resolved;
	}

	/**
	 * Checks if description is an example
	 * If yes, there will be displayed additional label and type
	 *
	 * @return boolean
	 */
	public function is_description_example() {
		return $this->description_example;
	}

	/**
	 * Gets merge tag resolved value
	 *
	 * @return mixed
	 */
	public function get_value() {
		return apply_filters( 'notification/merge_tag/' . $this->get_slug() . '/value', $this->value, $this );
	}

	/**
	 * Sets trigger object
	 *
	 * @since 5.0.0
	 * @param Interfaces\Triggerable $trigger Trigger object.
	 */
	public function set_trigger( Interfaces\Triggerable $trigger ) {
		$this->trigger = $trigger;
	}

	/**
	 * Sets resolver function
	 *
	 * @since 5.2.2
	 * @param mixed $resolver Resolver, can be either a closure or array or string.
	 */
	public function set_resolver( $resolver ) {

		if ( ! is_callable( $resolver ) ) {
			trigger_error( 'Merge tag resolver has to be callable', E_USER_ERROR );
		}

		$this->resolver = $resolver;

	}

	/**
	 * Gets trigger object
	 *
	 * @since 5.0.0
	 * @return Trigger object.
	 */
	public function get_trigger() {
		return $this->trigger;
	}

	/**
	 * Gets value type
	 *
	 * @since 5.0.0
	 * @return string
	 */
	public function get_value_type() {
		return $this->value_type;
	}

	/**
	 * Checks if merge tag is hidden
	 *
	 * @since 5.1.3
	 * @return boolean
	 */
	public function is_hidden() {
		return $this->hidden;
	}

	/**
	 * Cleans the value
	 *
	 * @since  5.2.2
	 * @return void
	 */
	public function clean_value() {
		$this->resolved = false;
		$this->value    = '';
	}

}
