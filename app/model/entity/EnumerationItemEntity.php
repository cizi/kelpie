<?php

namespace App\Model\Entity;


class EnumerationItemEntity {

	/** @var  int */
	private $id;

	/** @var  string */
	private $enumHeaderId;

	/** @var  string */
	private $lang;

	/** @var string */
	private $item;

	/** @var int */
	private $order;

    /** @var bool $isAke */
    private $isAke = false;

    /** @var bool $isWcc */
    private $isWcc = false;

    /** @var bool $isWcp */
    private $isWcp = false;

    /** @var string $health_group */
    private $health_group = "-";

	/**
	 * @return int
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * @param int $order
	 */
	public function setOrder($order) {
		$this->order = $order;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getEnumHeaderId() {
		return $this->enumHeaderId;
	}

	/**
	 * @param string $enumHeaderId
	 */
	public function setEnumHeaderId($enumHeaderId) {
		$this->enumHeaderId = $enumHeaderId;
	}

	/**
	 * @return string
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * @param string $lang
	 */
	public function setLang($lang) {
		$this->lang = $lang;
	}

	/**
	 * @return string
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @param string $item
	 */
	public function setItem($item) {
		$this->item = $item;
	}


    public function isAke(): bool
    {
        return $this->isAke;
    }

    public function setIsAke(bool $isAke): void
    {
        $this->isAke = $isAke;
    }

    public function isWcc(): bool
    {
        return $this->isWcc;
    }

    public function setIsWcc(bool $isWcc): void
    {
        $this->isWcc = $isWcc;
    }

    public function isWcp(): bool
    {
        return $this->isWcp;
    }

    public function setIsWcp(bool $isWcp): void
    {
        $this->isWcp = $isWcp;
    }

    public function getHealthGroup(): string
    {
        return $this->health_group;
    }

    public function setHealthGroup(string $health_group): void
    {
        $this->health_group = $health_group;
    }

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->id,
			'enum_header_id' => $this->enumHeaderId,
			'lang' => $this->lang,
			'item' => $this->item,
			'order' => $this->order,
            'is_ake' => $this->isAke,
            'is_wcc' => $this->isWcc,
            'is_wcp' => $this->isWcp,
            'health_group' => $this->health_group,
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->id = (isset($data['id']) ? $data['id'] : null);
		$this->enumHeaderId = $data['enum_header_id'];
		$this->lang = $data['lang'];
		$this->item = $data['item'];
		$this->order = $data['order'];
        $this->isAke = $data['is_ake'];
        $this->isWcc = $data['is_wcc'];
        $this->isWcp = $data['is_wcp'];
        $this->health_group = $data['health_group'];
	}
}
