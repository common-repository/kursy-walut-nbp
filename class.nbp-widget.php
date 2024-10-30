<?php

/**
* NBP
*/
class NBP_Widget extends WP_Widget {

	private static $actual_exchange_rates_uri = 'http://api.nbp.pl/api/exchangerates/tables/a';
	private $actual_exchange_rates = array();

	function __construct()
	{

		$temp_actual_exchange_rates = file_get_contents(static::$actual_exchange_rates_uri);
		$this->actual_exchange_rates = json_decode($temp_actual_exchange_rates);

		parent::__construct(
			'nbp-widget',
			__('Średni kurs walut - NBP', 'nbp-kurs-walut'),
			array(
				'descriptions' => __('Średni kursy walut Narodowego Banku Polskiego', 'nbp-kurs-walut')
			)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		?>

		<?php if( 'on' == $instance[ 'show_date' ] ) : ?>

			<time><small><?php _e( 'Kurs z dnia:', $domain ); ?> <?php echo date_i18n( get_option( 'date_format' ), strtotime( $this->actual_exchange_rates[0]->effectiveDate ) ); ?></small></time>

	  <?php endif; ?>

		<?php if (isset($this->actual_exchange_rates[0]->rates)): ?>
			<table>

		  	<tr>
		  		<th><?php _e( 'Waluta', 'nbp-kurs-walut' ); ?></th>
		  		<th><?php _e( 'Kurs', 'nbp-kurs-walut' ); ?></th>
		  	</tr>

			<?php if ($instance['show_code_or_currency'] === 'currency'): ?>
				
				<?php foreach ($this->actual_exchange_rates[0]->rates as $key => $value): ?>
					<?php if ($instance[$value->code] === 'on'): ?>
						<tr>
							<td style="width: 60%;"><?php echo ucfirst($value->currency); ?></td>
							<td style="vertical-align: middle;"><?php echo $value->mid; ?> <?php _e( 'PLN', 'nbp-kurs-walut' ); ?></td>
						</tr>
					<?php endif ?>
				<?php endforeach; ?>

			<?php else: ?>
				
				<?php foreach ($this->actual_exchange_rates[0]->rates as $key => $value): ?>
					<?php if ($instance[$value->code] === 'on'): ?>
					<tr>
						<td style="width: 60%;">
							<img style="width: 20%; vertical-align: middle; margin-right: 5px;" src="<?php echo plugins_url('images/' . $value->code . '.png', __FILE__); ?>" alt="<?php echo $value->code; ?>">
							<?php echo $value->code; ?>
						</td>
						<td style="vertical-align: middle;"><?php echo $value->mid; ?> <?php _e( 'PLN', 'nbp-kurs-walut' ); ?></td>
					</tr>
					<?php endif; ?>
				<?php endforeach; ?>

			<?php endif ?>
			</table>
		<?php
		endif;

    if( 'on' == $instance[ 'show_currency' ] ) : ?>
        <div class="about-us-avatar">
            <?php echo 'my checkbox' ?>
        </div>
    <?php endif;

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance['show_date'] = $new_instance['show_date'];
		$instance['show_code_or_currency'] = $new_instance['show_code_or_currency'];
		if (isset($this->actual_exchange_rates[0]->rates)):
			foreach ($this->actual_exchange_rates[0]->rates as $value):
				$instance[$value->code] = $new_instance[$value->code];
			endforeach;
		endif;

		return $instance;
	}

	public function form($instance)
	{

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Kurs Walut NBP', 'wpb_widget_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<p>Pokaż waluty: </p>
			<input type="radio" name="<?php echo $this->get_field_name('show_code_or_currency') ?>" value="code" <?php echo $instance['show_code_or_currency'] === 'code' ? 'checked="checked"' : ''; ?>><?php _e( 'Kod', 'nbp-kurs-walut' ); ?>
			<input type="radio" name="<?php echo $this->get_field_name('show_code_or_currency') ?>" value="currency" <?php echo $instance['show_code_or_currency'] === 'currency' ? 'checked="checked"' : ''; ?>> <?php _e( 'Nazwę', 'nbp-kurs-walut' ); ?><br />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('show_date'); ?>" id="<?php echo $this->get_field_id('show_date'); ?>" <?php checked( $instance['show_date'], 'on'); ?>>
			<label for="<?php echo $this->get_field_id('show_date') ?>"><?php _e( 'Pokaż datę', 'nbp-kurs-walut' ); ?></label>
		</p>

		<?php if (isset($this->actual_exchange_rates[0]->rates)): ?>
			<p>
			<p><?php _e( 'Pokaż walutę', 'nbp-kurs-walut' ); ?>:</p>
			<?php foreach ($this->actual_exchange_rates[0]->rates as $key => $value): ?>
				<span style="width: 30px;display: inline-block;overflow: hidden;text-align: center;">
				<input type="checkbox" name="<?php echo $this->get_field_name($value->code); ?>" id="<?php echo $this->get_field_id($value->code); ?>" <?php checked( $instance[$value->code], 'on'); ?>>
				<label for="<?php echo $this->get_field_id($value->code) ?>"><?php echo $value->code; ?></label>
				</span>
			<?php endforeach ?>
			</p>
		<?php 
		endif;
	}

}