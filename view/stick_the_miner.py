# Provably Rare Gem Miner
# love ya alpha team. <3
# by yoyoismee.eth
# noti by fluk
from Crypto.Hash import keccak
from eth_abi.packed import encode_abi_packed
import random
import time
import requests
import datetime

chain_id = 1  # eth main net who mine others chain for god sake // nah JK I think you know how to do chain ID
entropy = 0x949f147e5fb733f43d887bd3f9455dad768029a75baa8c63ec2d749439935d59  # loot / main net
# entropy = 0xe562c6985e1e24ea9e1b39595afc64ac6cee3a06f6f4402694a85f49a7986ba8 # bloot / main net
gemAddr = '0xC67DED0eC78b849e17771b2E8a7e303B4dAd6dD4'  # gem address (yeah at this point you should know what it is)
userAddr = '0x6460d47F50ad8d2480E136515403aD08Efb34230'  # your address. this is my address (where you can donate lol)
kind = 9 # which gem ya want (Loot, Amethyst = 0 Topaz = 1 ... for Bloot Violet=10, Goldy Pebble =1 ...)
nonce = 0  # how greedy are you? JK (you can read from contract or FE)
diff = 83886080002441406  # just read from the contract or front end
# diff = 281474976972799  # just read from the contract or front end azure skystone
# https://gems.alphafinance.io/#/   get diff, kind
# เช็คค่าทำเนียม https://etherscan.io/address/0xc67ded0ec78b849e17771b2e8a7e303b4dad6dd4?fbclid=IwAR30vjJbMXaEM4NolS0rkRvNPiIbOFYWzZmMKVkOvZfq5dVCuIwib2xL1ks#writeProxyContract
target = 2 ** 256 / diff

url = 'https://notify-api.line.me/api/notify'
token = 'O6DDWXKokuuzKObESpXSBbpckVdvUIzmJoEcVVIvWHu'
headers = {'content-type':'application/x-www-form-urlencoded','Authorization':'Bearer '+token}


def pack_mine(chain_id, entropy, gemAddr, senderAddr, kind, nonce, salt) -> bytes:
    return encode_abi_packed(['uint256', 'uint256', 'address', 'address', 'uint', 'uint', 'uint'],
                             (chain_id, entropy, gemAddr, senderAddr, kind, nonce, salt))


def mine(packed) -> (str, int):
    k = keccak.new(digest_bits=256)
    k.update(packed)
    hx = k.hexdigest()
    return hx, int(hx, base=16)


def get_salt() -> int:
    return random.randint(1, 2 ** 123)  # can probably go to 256 but 123 probably enough


i = 0
st = time.time()
dateStart = datetime.datetime.now()
firstMsg = "Starting Gem Minning... \n"
firstMsg += "Kind: " + str(kind) + "\n"
firstMsg += "Wallet: " + userAddr + "\n"
firstMsg += "Nonce:" + str(nonce) + "\n"
firstMsg += "Difficulty: " + str(diff) + "\n"
firstMsg += "Time: " + str(dateStart) + "\n"
firstNoti = requests.post(url, headers=headers, data = {'message':firstMsg})


while True:
    i += 1
    salt = get_salt()
    # salt = i
    hx, ix = mine(pack_mine(chain_id, entropy, gemAddr, userAddr, kind, nonce, salt))

    if ix < target:
        print("done! here's the salt - ", salt)
        dateEnd = datetime.datetime.now()
        difference = dateEnd - dateStart
        datetime.timedelta(0, 8, 562000)
        seconds_in_day = 24 * 60 * 60
        dateDiff = divmod(difference.days * seconds_in_day + difference.seconds, 60)
        
        if len(dateDiff) == 4:
            dateDiffSuccess = str(dateDiff[0]) +"วัน " + str(dateDiff[1]) +"ชั่วโมง " + str(dateDiff[2]) +"นาที " + str(dateDiff[3]) + "วินาที"
        elif len(dateDiff) == 3:
            dateDiffSuccess = str(dateDiff[0]) +"ชั่วโมง " + str(dateDiff[1]) +"นาที " + str(dateDiff[2]) + "วินาที"
        elif len(dateDiff) == 2:
            dateDiffSuccess =  str(dateDiff[0]) +"นาที " + str(dateDiff[1]) + "วินาที"

        strSalt = str(salt)
        msg = "Gem: Gem found \n"
        msg += "Kind: " + str(kind) + "\n"
        msg += "Wallet: " + userAddr + "\n"
        msg += "Nonce:" + str(nonce) + "\n"
        msg += "Difficulty: " + str(diff) + "\n"
        msg += "Salt: " + strSalt + "\n"
        msg += "Use time: " + dateDiffSuccess + "\n"
        r = requests.post(url, headers=headers, data = {'message':msg})
        print (r.text)
        break
    if i % 5000 == 0:
        print(f'iter {i}, {i / (time.time() - st)} avg iter per sec')




