			<div class="tr labelTR tr-noPadding">
				<label id="label_name" class="medText">Name</label>
				<label id="label_professions" class="medText">Profession(s)</label>
			</div>
			<div class="tr dataTR">
				<div class="medText"><?=$this->getName()?></div>
				<div class="medText"><? $this->displayClasses(); ?></div>
			</div>
			
			<div class="clearfix">
				<div id="stats">
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
?>
					<div class="tr">
						<label id="label_<?=$short?>" class="shortText leftLabel"><?=$stat?></label>
						<div class="stat"><?=$this->getStat($short)?></div>
						<span id="<?=$short?>Modifier"><?=$this->getStatMod($short)?></span>
					</div>
<?
		$statBonus[$short] = $bonus;
	}
?>
				</div>
				
				<div id="savingThrows">
					<div class="tr labelTR">
						<div class="fillerBlock cell">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Base</label>
						<label class="statSelect lrBuffer">Ability</label>
						<label class="shortNum lrBuffer">Magic</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
<?	foreach (d20Character_consts::getSaveNames() as $save => $saveFull) { ?>
					<div id="<?=$save?>Row" class="tr dataTR">
						<label class="leftLabel"><?=$saveFull?></label>
						<div id="fortTotal" class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'total'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'base'))?></div>
						<div class="statSelect lrBuffer">
							<div class="statShort"><?=ucwords($this->getSave($save, 'stat'))?></div>
							<div class="shortNum"><?=$this->getStatMod($this->getSave($save, 'stat'))?></div>
						</div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'magic'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'misc'))?></div>
					</div>
<?	} ?>
				</div>
				
				<div id="hp" class="dataTR">
					<div class="tr">
						<label class="leftLabel textLabel">Total HP</label>
						<div><?=$this->getHP('total')?></div>
						<label class="leftLabel textLabel">Subdual HP</label>
						<div><?=$this->getHP('subdual')?></div>
					</div>
					<div class="tr">
						<label class="leftLabel textLabel">Max Sanity</label>
						<div><?=$this->getSanity('max')?></div>
						<label class="leftLabel textLabel">Current Sanity</label>
						<div><?=$this->getSanity('current')?></div>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="ac">
					<div class="tr labelTR">
						<label class="first">Total AC</label>
						<div class="fillerBlock cell medNum">&nbsp;</div>
						<label>Armor</label>
						<label>Dex</label>
						<label>Misc</label>
					</div>
					<div class="tr dataTR">
						<div class="first"><?=$this->getAC('total')?></div>
						<div> = 10 + </div>
						<div><?=showSign($this->getAC('armor'))?></div>
						<div><?=showSign($this->getAC('dex'))?></div>
						<div><?=showSign($this->getAC('misc'))?></div>
					</div>
				</div>
				<div id="speed">
					<label class="leftLabel textLabel">Speed</label>
					<div class="cell shortNum alignCenter"><?=$this->getSpeed()?></div>
				</div>
			</div>

			<div id="combatBonuses" class="clearFix">
				<div class="tr labelTR">
					<div class="fillerBlock cell shortText">&nbsp;</div>
					<label class="shortNum">Total</label>
					<label class="shortNum">Base</label>
					<label class="statSelect">Ability</label>
					<label class="shortNum">Misc</label>
				</div>
				<div id="init" class="tr dataTR">
					<label class="leftLabel shortText">Initiative</label>
					<span id="initTotal" class="shortNum"><?=showSign($this->getInitiative('total'))?></span>
					<span class="shortNum">&nbsp;</span>
					<span class="statSelect">
						<span class="statShort"><?=ucwords($this->getInitiative('stat'))?></span>
						<span class="shortNum"><?=$this->getStatMod($this->getInitiative('stat'))?></span>
					</span>
					<div class="shortNum"><?=showSign($this->getInitiative('misc'))?></div>
				</div>
				<div id="melee" class="tr dataTR">
					<label class="leftLabel shortText">Melee</label>
					<span id="meleeTotal" class="shortNum"><?=showSign($this->getAttackBonus('total', 'melee') + $this->getStatMod('str'))?></span>
					<div class="shortNum"><?=showSign($this->getAttackBonus('base'))?></div>
					<span class="statSelect">
						<span class="statShort"><?=ucwords($this->getAttackBonus('stat', 'melee'))?></span>
						<span class="shortNum"><?=$this->getStatMod($this->getAttackBonus('stat', 'melee'))?></span>
					</span>
					<div class="shortNum"><?=showSign($this->getAttackBonus('misc', 'melee'))?></div>
				</div>
				<div id="ranged" class="tr dataTR">
					<label class="leftLabel shortText">Ranged</label>
					<span id="rangedTotal" class="shortNum"><?=showSign($this->getAttackBonus('total', 'ranged') + $this->getStatMod('dex'))?></span>
					<span class="shortNum bab"><?=showSign($this->getAttackBonus('base'))?></span>
					<span class="statSelect">
						<span class="statShort"><?=ucwords($this->getAttackBonus('stat', 'ranged'))?></span>
						<span class="shortNum"><?=$this->getStatMod($this->getAttackBonus('stat', 'ranged'))?></span>
					</span>
					<div class="shortNum"><?=showSign($this->getAttackBonus('misc', 'ranged'))?></div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div class="tr labelTR">
							<label class="medText">Skill</label>
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="shortNum alignCenter lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Ranks</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
						</div>
<?	$this->displaySkills(); ?>
					</div>
				</div>
				<div id="feats" class="floatRight">
					<h2 class="headerbar hbDark">Feats/Abilities</h2>
					<div class="hbdMargined">
<?	$this->displayFeats(); ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons</h2>
					<div class="hbdMargined">
<?	$this->displayWeapons(); ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="items">
					<h2 class="headerbar hbDark">Items</h2>
					<div class="hbdMargined"><?=printReady($this->getItems())?></div>
				</div>
				
				<div id="spells">
					<h2 class="headerbar hbDark">Spells</h2>
					<div class="hbdMargined"><?=printReady($this->getSpells())?></div>
				</div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=printReady($this->getNotes())?></div>
			</div>
