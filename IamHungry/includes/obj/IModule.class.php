<?php

/**
 * Interface IModule
 * Describes modules functioning
 */
interface IModule
{
	public function __construct();
	public function preProcess($construct);
	public function display();
}