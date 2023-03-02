<?php

namespace YektaSmart\IotServer\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface IHardwareManager
{
    /**
     * @param array{owner?:int|null,name?:string,version?:string,compatibleWithProduct?:int|IProduct,compatibleWithFrameware?:int|IFrameware} $filters
     *
     * @return iterable<IHardware>
     */
    public function search(array $filters): iterable;

    /**
     * @param array<IProduct|int>  $products
     * @param array<IFramware|int> $frameware
     */
    public function store(
        string $name,
        string $version,
        int|Authenticatable $owner,
        array $products,
        array $frameware,
        bool $userActivityLog = false
    ): IHardware;

    /**
     * @param array{name?:string,version?:string,owner?:int|Authenticatable,products?:array<IProduct|int>,frameware?:array<IFrameware|int>}
     */
    public function update(int|IHardware $hardware, array $changes, bool $userActivityLog = false): IHardware;

    public function destroy(int|IHardware $hardware, bool $userActivityLog = false): void;
}
